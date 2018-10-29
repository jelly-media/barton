<?php

class Frizzly_Ajax_Result_Builder {
    
    private $model;
    private $status;
    private $messages;

    function __construct() {
        $this->model = null;
        $this->messages = array();
        $this->status = 'OK';
    }

    function set_model($model) {
        $this->model = $model;
        return $this;
    }

    function set_error($error_msg, $error_msg_id = 'error') {
        $arr = array();
        $arr[$error_msg_id] = $error_msg;
        return $this->set_errors($arr);
    }

    function set_errors($errors) {
        $this->status = 'ERROR';
        $this->messages = $errors;
        return $this;
    }

    function set_message($msg, $msg_id = 'ok'){
        $this->messages = array($msg_id => $msg);
        return $this;
    }

    function build() {
        $res = array(
            'status' => array(
                'status' => $this->status,
                'messages' => $this->messages
            )
        );

        if ($this->model != null)
            $res['model'] = $this->model;
        
        return $res;
    }
}