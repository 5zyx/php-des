<?php
/**
 * Created by PhpStorm.
 * User: Jaylee
 * Date: 16/3/02
 * Time: 23:01
 */

namespace DES;

use Whoops\Example\Exception;

class Des {

    /** AES **/
    const MODE_AES = 1;

    /** DES **/
    const MODE_DES = 2;

    /** 3DES **/
    const MODE_3DES = 3;

    private static $_HANDLE_ARRAY = [];

    private function __construct() {
    }

    private function __clone() {
    }

    /**
     * @desc 获取句柄标识
     * @param $params
     * @return mixed
     */
    private static function _getHandleKey($params){
        ksort($params);
        return md5(implode('_', $params));
    }

    /**
     * @desc 创建一个加密对象
     * @param $mode 加密类型 
     * @param $secretKey    密钥
     * @param null $iv      IV
     * @return mixed
     * @throws Exception
     */
    public static function getInstance( $mode, $secretKey, $iv = null ){

        if ( empty($secretKey) ){
            throw new Exception( sprintf( "Fail, aesKey不能为空.") );
        }

        $handle_key = self::_getHandleKey([
            'mode'      =>  $mode,
            'secretKey' =>  $secretKey
        ]);

        if ( !isset( self::$_HANDLE_ARRAY[$handle_key] )){

            switch ( $mode ){
                case self::MODE_DES:
                    $obj = new Adapter\DesEncrypt( $secretKey );
                    break;
                case self::MODE_3DES:
                    $obj = new Adapter\Des3Encrypt( $secretKey, $iv);
                    break;
                case self::MODE_AES:
                default:
                    $obj = new Adapter\AesEncrypt( $secretKey );
                    break;
            }

            self::$_HANDLE_ARRAY[$handle_key] = $obj;
        }

        return self::$_HANDLE_ARRAY[$handle_key];
    }
}


spl_autoload_register(function( $className ){

    $pos = strpos($className, '\\');

    $_relativePath = substr( $className, $pos + 1);

    $_filePath = __DIR__ . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $_relativePath ) . '.php';

    if (is_file( $_filePath )){
        require $_filePath;
    }
});


$aes = Des::getInstance( DES::MODE_AES, 'des_key' );

var_dump($aes->encrypt('abcdefg'));

var_dump( $aes->decrypt('GlU3LwJlug19WbslKWcG') );

echo "<hr />";

$des = Des::getInstance( DES::MODE_DES, '12345678' );

var_dump($des->encrypt('abcdefg'));

var_dump( $des->decrypt('7vi5Zfc+qQ4='));

echo "<hr />";

$des3 = Des::getInstance( Des::MODE_3DES, '&*(&Q13S', "3)(!U1#!" );

var_dump( $des3->encrypt('abcdefg'));

var_dump( $des3->decrypt('Et14K5NpEgY=') );


