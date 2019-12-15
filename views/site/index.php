<?php

/* @var $this yii\web\View */

use yii\bootstrap\Collapse;
use yii\helpers\Html;
use \yii\helpers\Url;

$this->title = Yii::t('app', 'Application');

?>

<div class="site-index">
    <div class="body-content">
        <div align="left" class="mainContentWrapper ">
            <div class="row row-fluid">
                <div align="center">
                </div>
                <br><br>
            </div>
            <div class="row row-fluid" style="margin:10px auto 15px">
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Нужна помощь?
                        </div>
                        <div class="panel-body">
                            Пожалуйста позвоните нам <span style="white-space: nowrap"><b>(099) 999-99-99</b>.<br><br>
                <a href="#" target="new" class="btn btn-default pull-right"><i class="fa fa-life-ring" style="color:green;"> </i> Contact</a>
            </span></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Обновления: <b><?php echo date('d-m-Y', mktime()); ?></b>
                        </div>
                        <div class="panel-body">
                            <div id="hero">
                                <div class="inner">
                                    <div class="left" style="width: 50%; float: left;">
                                        <img class="hero-logo" src="https://ru.vuejs.org/images/logo.png" alt="vue logo">
                                    </div><div class="right" style="">
                                        <h1>
                                            The Progressive<br>JavaScript Framework
                                            <div id="demoVueElement"></div>
                                        </h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-fluid">
                <div align="left" class="col-xs-12 col-md-10 col-lg-8">
                </div>
            </div>
        </div>
    </div>
</div>

