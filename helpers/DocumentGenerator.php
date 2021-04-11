<?php

namespace app\helpers;

use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

abstract class DocumentGenerator
{
    protected $fileName = null;

    /**
     * @return null
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param null $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    abstract protected function generate();
    abstract protected function download();
}
