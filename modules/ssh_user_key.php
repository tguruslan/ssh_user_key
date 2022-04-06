<?php

class FixPrmission
{
    /**
     * Now action
     * @var string
     */
    private $action;

    /**
     * Smarty
     * @var object
     */
    public $smarty;

    /**
     * Smarty template
     * @var object
     */

    public $tpl;

    /**
     * @var 2FAController
     */

    private static $instance;

    private static $users = false;
    private static $usersDir = '/var/brainycp/data/users/';

    public static function getUsers()
    {
        if (self::$users === false) {
            if ($handle = opendir(self::$usersDir)) {
                self::$users = array();
                while (false !== ($file = readdir($handle))) {
                    if (is_dir(self::$usersDir . $file) || in_array($file, array('.', '..'))) {
                        continue;
                    }
                    $config = parse_ini_file(self::$usersDir.$file);
                    self::$users[$config['username']] = $config['username'];
                }
                closedir($handle);
            }
        }

        sort(self::$users);

        return self::$users;
    }

    private function get_keyAction(){
        $user=$_POST['user'];

        if ($handle = opendir(self::$usersDir)) {
            self::$users = array();
            while (false !== ($file = readdir($handle))) {
                if (is_dir(self::$usersDir . $file) || in_array($file, array('.', '..'))) {
                    continue;
                }
                $config = parse_ini_file(self::$usersDir.$file);
                if($user == $config['username']){
                    $keyfile=$config['rootdir'].'/sites/.ssh/authorized_keys';
                }
            }
            closedir($handle);
        }
        if(is_file($keyfile)){
            $key=file_get_contents($keyfile);
        }else{
            $key="";
        }


        $this->smarty->assign('key', $key);
        $this->tpl->out = $this->smarty->fetch('ssh_user_key/key.tpl');

        echo $this->tpl->out;
        die();
    }

    private function do_saveAction(){

        global $server;
        global $lang;
        $user=$_POST['user'];
        $content=$_POST['content'];

        if(!$user){$user=$server->user['username'];}

        if ($handle = opendir(self::$usersDir)) {
            self::$users = array();
            while (false !== ($file = readdir($handle))) {
                if (is_dir(self::$usersDir . $file) || in_array($file, array('.', '..'))) {
                    continue;
                }
                $config = parse_ini_file(self::$usersDir.$file);
                if($user == $config['username']){
                    $keydir=$config['rootdir'].'/sites/.ssh';
                    $keyfile=$config['rootdir'].'/sites/.ssh/authorized_keys';
                }
            }
            closedir($handle);
        }

        if(!is_dir($keydir)){
           mkdir($keydir, 0700, true);
           chown($keydir, $user);
           chgrp($keydir, $user);
        }
        if(!is_file($keyfile)){
            touch($keyfile);
            chmod($keyfile, 0600);
            chown($keyfile,$user);
            chgrp($keyfile,$user);
        }

        $status=file_put_contents($keyfile, $content);



        if ($status == strlen($content)){
             $msg=$lang['success'];
        }else{
             $msg=$lang['error'];
        }
        echo json_encode(['message'=>$msg]);
        die();
    }

    /**
     * Instantiate and return a factory.
     * @return 2FAController
     */

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Start init page
     * @param object $smarty
     * @param object $tpl
     */

    public function init($smarty, $tpl)
    {
        $this->smarty = $smarty;
        $this->tpl = $tpl;

        $this->setAction();

        if (!method_exists($this, $this->action . 'Action')) {
            $this->action = 'default';
        }

        $methodName = $this->action . 'Action';
        $this->$methodName();
    }

    /**
     * Default index page
     */

    private function defaultAction()
    {
        global $server;
        $this->smarty->assign('users', self::getUsers());
        $this->smarty->assign('g_userinfo', $server->user);


         if(($server->user.group_properties.root != "y") && ($server->user.users_management != "y")){
            $user=$server->user['username'];

            if ($handle = opendir(self::$usersDir)) {
                self::$users = array();
                while (false !== ($file = readdir($handle))) {
                    if (is_dir(self::$usersDir . $file) || in_array($file, array('.', '..'))) {
                        continue;
                    }
                    $config = parse_ini_file(self::$usersDir.$file);
                    if($user == $config['username']){
                        $keyfile=$config['rootdir'].'/sites/.ssh/authorized_keys';
                    }
                }
                closedir($handle);
            }
            if(is_file($keyfile)){
                $key=file_get_contents($keyfile);
            }else{
                $key="";
            }


            $this->smarty->assign('key', $key);
         }


        $this->tpl->out = $this->smarty->fetch('ssh_user_key/main.tpl');
    }

    /**
     * Auto set action from REQUEST
     */

    public function setAction()
    {
        if (isset($_REQUEST['subdo'])) {
            $this->action = $_REQUEST['subdo'];
        }
    }
}

FixPrmission::getInstance()->init($smarty, $tpl);
