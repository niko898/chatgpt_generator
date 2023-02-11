<?php
class ControllerExtensionModuleChatgptGenerator extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/chatgpt_generator');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_chatgpt_generator', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['error_api_key'])) {
            $data['error_api_key'] = $this->error['error_api_key'];
        } else {
            $data['error_api_key'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/account', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/chatgpt_generator', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['module_chatgpt_generator_status'])) {
            $data['status'] = $this->request->post['module_chatgpt_generator_status'];
        } else {
            $data['status'] = $this->config->get('module_chatgpt_generator_status');
        }

        if (isset($this->request->post['module_chatgpt_generator_api_key'])) {
            $data['api_key'] = $this->request->post['module_chatgpt_generator_api_key'];
        } else {
            $data['api_key'] = $this->config->get('module_chatgpt_generator_api_key');
        }

        if (isset($this->request->post['module_chatgpt_generator_prompt'])) {
            $data['prompt'] = $this->request->post['module_chatgpt_generator_prompt'];
        } else {
            $data['prompt'] = $this->config->get('module_chatgpt_generator_prompt');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/chatgpt_generator', $data));
    }

    public function testRequest(){
        $json = array();
        if(isset($this->request->get['prompt'])){
            $prompt = (string) $this->request->get['prompt'];
            $this->load->model('tool/chatgpt_generator');
            $result = $this->model_tool_chatgpt_generator->generate($prompt);
            if(isset($result['choices'][0]['text'])){
                $json['result'] = $result['choices'][0]['text'];
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/chatgpt_generator')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['module_chatgpt_generator_api_key']) < 3) || (utf8_strlen($this->request->post['module_chatgpt_generator_api_key']) > 256)) {
            $this->error['error_api_key'] = $this->language->get('error_api_key');
        }

        return !$this->error;
    }
}