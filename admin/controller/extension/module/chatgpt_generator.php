<?php
class ControllerExtensionModuleChatgptGenerator extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/chatgpt_generator');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');
        $this->load->model('tool/chatgpt_generator');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_chatgpt_generator', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/chatgpt_generator', 'user_token=' . $this->session->data['user_token'], true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->error['error_api_key'])) {
            $data['error_api_key'] = $this->error['error_api_key'];
        } else {
            $data['error_api_key'] = '';
        }

        if (isset($this->error['error_temperature'])) {
            $data['error_temperature'] = $this->error['error_temperature'];
        } else {
            $data['error_temperature'] = '';
        }

        if (isset($this->error['error_max_tokens'])) {
            $data['error_max_tokens'] = $this->error['error_max_tokens'];
        } else {
            $data['error_max_tokens'] = '';
        }

        if (isset($this->error['error_top_p'])) {
            $data['error_top_p'] = $this->error['error_top_p'];
        } else {
            $data['error_top_p'] = '';
        }

        if (isset($this->error['error_presence_penalty'])) {
            $data['error_presence_penalty'] = $this->error['error_presence_penalty'];
        } else {
            $data['error_presence_penalty'] = '';
        }

        if (isset($this->error['error_frequency_penalty'])) {
            $data['error_frequency_penalty'] = $this->error['error_frequency_penalty'];
        } else {
            $data['error_frequency_penalty'] = '';
        }

        if (isset($this->error['error_model'])) {
            $data['error_model'] = $this->error['error_model'];
        } else {
            $data['error_model'] = '';
        }

        if (isset($this->error['error_stop'])) {
            $data['error_stop'] = $this->error['error_stop'];
        } else {
            $data['error_stop'] = '';
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

        $data["chatgpt_generator_cron"] = "php " . realpath(DIR_SYSTEM . "../cron/chatgpt_generator.php");
        $data["chatgpt_generator_web_cron"] = HTTP_CATALOG . "cron/chatgpt_generator.php";


        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['module_chatgpt_generator_status'])) {
            $data['status'] = $this->request->post['module_chatgpt_generator_status'];
        } else {
            $data['status'] = $this->config->get('module_chatgpt_generator_status');
        }

        if (isset($this->request->post['module_chatgpt_generator_debug_status'])) {
            $data['debug_status'] = $this->request->post['module_chatgpt_generator_debug_status'];
        } else {
            $data['debug_status'] = $this->config->get('module_chatgpt_generator_debug_status');
        }

        if (isset($this->request->post['module_chatgpt_generator_product_description_status'])) {
            $data['product_description_status'] = $this->request->post['module_chatgpt_generator_product_description_status'];
        } else {
            $data['product_description_status'] = $this->config->get('module_chatgpt_generator_product_description_status');
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

        if (isset($this->request->post['module_chatgpt_generator_temperature'])) {
            $data['temperature'] = $this->request->post['module_chatgpt_generator_temperature'];
        } else {
            $data['temperature'] = $this->config->get('module_chatgpt_generator_temperature');
        }

        if (isset($this->request->post['module_chatgpt_generator_max_tokens'])) {
            $data['max_tokens'] = $this->request->post['module_chatgpt_generator_max_tokens'];
        } else {
            $data['max_tokens'] = $this->config->get('module_chatgpt_generator_max_tokens');
        }

        if (isset($this->request->post['module_chatgpt_generator_top_p'])) {
            $data['top_p'] = $this->request->post['module_chatgpt_generator_top_p'];
        } else {
            $data['top_p'] = $this->config->get('module_chatgpt_generator_top_p');
        }

        if (isset($this->request->post['module_chatgpt_generator_presence_penalty'])) {
            $data['presence_penalty'] = $this->request->post['module_chatgpt_generator_presence_penalty'];
        } else {
            $data['presence_penalty'] = $this->config->get('module_chatgpt_generator_presence_penalty');
        }

        if (isset($this->request->post['module_chatgpt_generator_frequency_penalty'])) {
            $data['frequency_penalty'] = $this->request->post['module_chatgpt_generator_frequency_penalty'];
        } else {
            $data['frequency_penalty'] = $this->config->get('module_chatgpt_generator_frequency_penalty');
        }

        if (isset($this->request->post['module_chatgpt_generator_model'])) {
            $data['model'] = $this->request->post['module_chatgpt_generator_model'];
        } else {
            $data['model'] = $this->config->get('module_chatgpt_generator_model');
        }

        if (isset($this->request->post['module_chatgpt_generator_stop'])) {
            $data['stop'] = $this->request->post['module_chatgpt_generator_stop'];
        } else {
            $data['stop'] = $this->config->get('module_chatgpt_generator_stop');
        }

        if (isset($this->request->post['module_chatgpt_generator_languages'])) {
            $data['languages'] = $this->request->post['module_chatgpt_generator_languages'];
        } else {
            $data['languages'] = $this->config->get('module_chatgpt_generator_languages');
        }

        $models = array(
            'text-davinci-003'  => 'text-davinci-003',
            'text-curie-001' => 'text-curie-001',
            'text-babbage-001' => 'text-babbage-001',
            'text-ada-001' => 'text-ada-001',
            'text-davinci-002' => 'text-davinci-002',
            'text-davinci-001' => 'text-davinci-001',
            'davinci' => 'davinci',
        );

        $data['models_data'] = $models;

        $this->load->model('localisation/language');
        $data['languages_data'] = $this->model_localisation_language->getLanguages();

        $data['product_work_count'] = 0;
        $product_work_count = $this->model_tool_chatgpt_generator->getProducts();
        if($product_work_count){
            $data['product_work_count'] = count($product_work_count);
        }

        $data['bad_product_work_count'] = 0;
        $bad_product_work_count = $this->model_tool_chatgpt_generator->getProductsBad();
        if($bad_product_work_count){
            $data['bad_product_work_count'] = count($bad_product_work_count);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/chatgpt_generator', $data));
    }

    public function setProductList(){
        $json = array();

        $this->load->model('tool/chatgpt_generator');
        $products = $this->model_tool_chatgpt_generator->getProductDescriptionEmpty();

        if(count($products)){
            foreach ($products as $product){
                $this->model_tool_chatgpt_generator->addProduct($product['product_id']);
            }
        }

        $product_work_count = $this->model_tool_chatgpt_generator->getProducts();
        $product_work_count = count($product_work_count);
        $json['product_work_count'] = trim($product_work_count);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function setAllProductList(){
        $json = array();

        $this->load->model('tool/chatgpt_generator');
        $this->load->model('catalog/product');
        $products = $this->model_catalog_product->getProducts();

        if(count($products)){
            foreach ($products as $product){
                $this->model_tool_chatgpt_generator->addProduct($product['product_id']);
            }
        }

        $product_work_count = $this->model_tool_chatgpt_generator->getProducts();
        $product_work_count = count($product_work_count);
        $json['product_work_count'] = trim($product_work_count);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function setBadProductList(){
        $json = array();

        $this->load->model('tool/chatgpt_generator');
        $products = $this->model_tool_chatgpt_generator->getProductsBad();

        if(count($products)){
            foreach ($products as $product){
                $this->model_tool_chatgpt_generator->addProduct($product['product_id']);
            }
        }

        $product_work_count = $this->model_tool_chatgpt_generator->getProducts();
        $product_work_count = count($product_work_count);
        $json['product_work_count'] = trim($product_work_count);

        $bad_product_work_count = $this->model_tool_chatgpt_generator->getProductsBad();
        $bad_product_work_count = count($bad_product_work_count);
        $json['bad_product_work_count'] = trim($bad_product_work_count);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function clearProductList(){
        $json = array();

        $this->load->model('tool/chatgpt_generator');
        $products = $this->model_tool_chatgpt_generator->getProducts();

        if(count($products)){
            foreach ($products as $product){
                $this->model_tool_chatgpt_generator->deleteProduct($product['product_id']);
            }
        }

        $product_work_count = $this->model_tool_chatgpt_generator->getProducts();
        $product_work_count = count($product_work_count);
        $json['product_work_count'] = trim($product_work_count);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function addManualProductList(){
        $json = array();
        $this->load->model('tool/chatgpt_generator');
        if(isset($this->request->post['product_manual']) && count($this->request->post['product_manual'])){
            foreach ($this->request->post['product_manual'] as $product_id){
                $this->model_tool_chatgpt_generator->addProduct($product_id);
            }
        }

        $product_work_count = $this->model_tool_chatgpt_generator->getProducts();
        $product_work_count = count($product_work_count);
        $json['product_work_count'] = trim($product_work_count);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function testRequest(){
        $json = array();
        if(isset($this->request->get['prompt'])){
            $prompt = (string) $this->request->get['prompt'];
            $this->load->model('tool/chatgpt_generator');
            $result = $this->model_tool_chatgpt_generator->generate($prompt);
            if(isset($result['choices'][0]['text'])){
                $json['result'] = trim($result['choices'][0]['text']);
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/chatgpt_generator')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ((utf8_strlen($this->request->post['module_chatgpt_generator_api_key']) < 8) || (utf8_strlen($this->request->post['module_chatgpt_generator_api_key']) > 256)) {
            $this->error['error_api_key'] = $this->language->get('error_api_key');
        }
        if ((utf8_strlen($this->request->post['module_chatgpt_generator_temperature']) < 1) || (utf8_strlen($this->request->post['module_chatgpt_generator_temperature']) > 256)) {
            $this->error['error_temperature'] = $this->language->get('error_temperature');
        }
        if ((utf8_strlen($this->request->post['module_chatgpt_generator_max_tokens']) < 1) || (utf8_strlen($this->request->post['module_chatgpt_generator_max_tokens']) > 256)) {
            $this->error['error_max_tokens'] = $this->language->get('error_max_tokens');
        }
        if ((utf8_strlen($this->request->post['module_chatgpt_generator_top_p']) < 1) || (utf8_strlen($this->request->post['module_chatgpt_generator_top_p']) > 256)) {
            $this->error['error_top_p'] = $this->language->get('error_top_p');
        }
        if ((utf8_strlen($this->request->post['module_chatgpt_generator_presence_penalty']) < 1) || (utf8_strlen($this->request->post['module_chatgpt_generator_presence_penalty']) > 256)) {
            $this->error['error_presence_penalty'] = $this->language->get('error_presence_penalty');
        }
        if ((utf8_strlen($this->request->post['module_chatgpt_generator_frequency_penalty']) < 1) || (utf8_strlen($this->request->post['module_chatgpt_generator_frequency_penalty']) > 256)) {
            $this->error['error_frequency_penalty'] = $this->language->get('error_frequency_penalty');
        }
        if ((utf8_strlen($this->request->post['module_chatgpt_generator_model']) < 1) || (utf8_strlen($this->request->post['module_chatgpt_generator_model']) > 256)) {
            $this->error['error_model'] = $this->language->get('error_model');
        }
        if ((utf8_strlen($this->request->post['module_chatgpt_generator_stop']) < 1) || (utf8_strlen($this->request->post['module_chatgpt_generator_stop']) > 256)) {
            //$this->error['error_stop'] = $this->language->get('error_stop');
        }

        return !$this->error;
    }

    public function install(){
        $this->load->model('extension/module/chatgpt_generator');
        $this->model_extension_module_chatgpt_generator->install();

    }
}