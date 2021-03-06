<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends RController
{

  const STATUS_ERROR = 'error';
  const STATUS_OK = 'OK';
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
//	public $layout='//layouts/column1';
  public $layout = 'content_only';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

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

  public function filters()
  {
    return array(
      'accessControl'
    );
  }

  public static function jsonAsnwer($data = null, $status = self::STATUS_OK, $message = '', $print = true){
    $json =  json_encode(array(
      'status' => $status,
      'message' => $message,
      'data' => $data
    ));
    if($print) {
      echo $json;
    }
    return $json;
  }

}
