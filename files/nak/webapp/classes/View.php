<?php

/*
 * This class View($template, $params) load a template.phtml file
 * by including this file. It passes the params to _params, so
 * they can be used by the view.
 */
class View
{
    protected $_template;
    protected $_params = array();

    public function __construct($template, $params = null)
    {
        /* Validate template name. */
        if (!preg_match("/^[a-zA-Z\_]*$/",$template)) {
            /* We throw a notfoundexception, so a caught break-in attempt
             * does not differ from a genuine not found exception. */
            throw new NotFoundException("View file does not exist.");
        }

        $filename = VIEW_DIR . "/$template.phtml";
        if (!file_exists($filename))
            throw new NotFoundException("View file does not exist.");

        $this->_template = $filename;
        $this->_params = $params;
    }

    public function display()
    {
        return include($this->_template);
    }

    protected function _displayMessages($form)
    {
        $flashMessager = new FlashMessager();
        $messages = $flashMessager->getMessages($form);

        $errors = $this->_getErrors($messages);
        if (!empty($errors)) {
            echo '<div class="tile error"><p><span class="fui-cross-circle">
                  </span>The following errors have occurred:<ul>';
            foreach ($errors as $error) {
                $text = htmlspecialchars($error['text']);
                echo "<li>$text</li>";
            }
            echo '</div></ul>';
        }

        $info = $this->_getInfo($messages);
        if (!empty($info)) {
            foreach ($info as $i) {
                $text = htmlspecialchars($i['text']);
                echo "<div class=\"tile info\"><p><span class=\"fui-check-circle\">
                      </span>$text</div>";
            }
        }
    }

    protected function _getInfo($messages) {
        $info = array();

        if (!empty($messages))
            foreach ($messages as $message)
                if ($message['type'] == 'info')
                    $info[] = $message;

        return $info;
    }

    protected function _getErrors($messages) {
        $errors = array();

        if (!empty($messages))
            foreach ($messages as $message)
                if ($message['type'] == 'error')
                    $errors[] = $message;

        return $errors;
    }

    protected function _getFormValue($form, $name)
    {
        $flashMessager = new FlashMessager();
        return $flashMessager->getFormData($form, $name);
    }

    protected function _getFormError($form, $name)
    {
        $flashMessager = new FlashMessager();
        return $flashMessager->getFormError($form, $name);
    }

    protected function _getFormToken()
    {
        return $_SESSION['token'];
    }
}
