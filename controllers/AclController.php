<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2018 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 * @link https://github.com/mdmsoft/yii2-admin/blob/master/controllers/RouteController.php#L196
 * @link http://ru.stackoverflow.com/questions/452581/%D0%9A%D0%B0%D0%BA-%D0%BF%D0%BE%D0%BB%D1%83%D1%87%D0%B8%D1%82%D1%8C-%D0%B2%D1%81%D0%B5-%D0%BA%D0%BE%D0%BD%D1%82%D1%80%D0%BE%D0%BB%D0%BB%D0%B5%D1%80%D1%8B-%D0%BF%D1%80%D0%B8%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D1%8F-%D0%B8-%D0%B8%D1%85-%D1%8D%D0%BA%D1%88%D0%B5%D0%BD%D1%8B-yii2
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Inflector;

use app\models\AcFunc;

class AclController extends Controller
{

    private $controllerActions = [];

    /**
     * Lists all Route models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $out = '';
        $this->getRouteRecrusive(Yii::$app);
        $out .= "<table>";
        foreach ($this->controllerActions as $key => $value) {
            $out .= "<tr>";
            $out .= "<td>";
            $out .= $value[0];
            $out .= "</td>";
            $out .= "<td>";
            $out .= $value[1];
            $out .= "</td>";
            $out .= "</tr>";
        }
        $out .= "</table>";
        foreach ($this->controllerActions as $key => $value) {
            if (strpos($value[0], 'debug/') !== false) continue;
            if (strpos($value[0], 'gii/') !== false) continue;
            if ( $acf = AcFunc::find()->where(['acf_controller' => $value[0], 'acf_action' => $value[1]])->one() ) {
                $acf->acf_controller = $value[0];
                $acf->acf_action = $value[1];
                $acf->save();
            } else {
                $acf = new AcFunc();
                $acf->acf_controller = $value[0];
                $acf->acf_action = $value[1];
                $acf->acf_name = $value[0] . '->' . $value[1];
                $acf->save();
            }
        }
        return $out;
    }

    /**
     * Get route(s) recrusive
     *
     * @param \yii\base\Module $module
     * @param array $result
     */
    private function getRouteRecrusive($module)
    {
        try {
            foreach ($module->getModules() as $id => $child) {
                if (($child = $module->getModule($id)) !== null) {
                    $this->getRouteRecrusive($child);
                }
            }
            foreach ($module->controllerMap as $id => $type) {
                $this->getControllerActions($type, $id, $module);
            }
            $namespace = trim($module->controllerNamespace, '\\') . '\\';
            $this->getControllerFiles($module, $namespace, '');
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
    }

    /**
     * Get list controller under module
     *
     * @param \yii\base\Module $module
     * @param string $namespace
     * @param string $prefix
     * @param mixed $result
     *
     * @return mixed
     */
    private function getControllerFiles($module, $namespace, $prefix)
    {
        /*
        $files = FileHelper::findFiles($path, [
            'only' => [
                '*Controller.php'
            ]
        ]);
        */

        $path = @Yii::getAlias('@' . str_replace('\\', '/', $namespace));
        try {
            if (!is_dir($path)) {
                return;
            }
            foreach (scandir($path) as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_dir($path . '/' . $file)) {
                    $this->getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/');
                } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                    $id = Inflector::camel2id(substr(basename($file), 0, -14));
                    $className = $namespace . Inflector::id2camel($id) . 'Controller';
                    if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                        $this->getControllerActions($className, $prefix . $id, $module);
                    }
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        return null;
    }

    /**
     * Get list action of controller
     *
     * @param mixed $type
     * @param string $id
     * @param \yii\base\Module $module
     * @param string $result
     */
    private function getControllerActions($type, $id, $module)
    {
        try {
            /* @var $controller \yii\base\Controller */
            $controller = Yii::createObject($type, [$id, $module]);
            $this->getActionRoutes($controller);
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
    }

    /**
     * Get route of action
     *
     * @param \yii\base\Controller $controller
     * @param array $result all controller action.
     */
    private function getActionRoutes($controller)
    {
        try {
            //$prefix = '/' . $controller->uniqueId . '/';
            foreach ($controller->actions() as $id => $value) {
                $this->controllerActions[] = [$controller->uniqueId, Inflector::camel2id(substr($value, 6))];
            }
            $class = new \ReflectionClass($controller);
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();

                if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                    $this->controllerActions[] = [$controller->uniqueId, Inflector::camel2id(substr($name, 6))];
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
    }

}
