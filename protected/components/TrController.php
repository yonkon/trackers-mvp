<?php

class TrController extends Controller
{
  public function redirect($url,$terminate=true,$statusCode=302)
  {
    if(is_array($url))
    {
      $route=isset($url[0]) ? $url[0] : '';
      $url=$this->createUrl($route,array_splice($url,1));
    }
    if(empty($url)) {
      $url = '/';
    }
    Yii::app()->getRequest()->redirect($url,$terminate,$statusCode);
  }

}
