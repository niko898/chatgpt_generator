<?php
class ModelExtensionModuleChatgptGenerator extends Model {
    public function install(){
        $this->load->model('setting/setting');
        $data = array(
            'module_chatgpt_generator_status' => 0,
            'module_chatgpt_generator_product_description_status' => 1,
            'module_chatgpt_generator_api_key' => '',
            'module_chatgpt_generator_model' => 'text-davinci-003',
            'module_chatgpt_generator_temperature' => 0.9,
            'module_chatgpt_generator_max_tokens' => 1000,
            'module_chatgpt_generator_top_p' => 1,
            'module_chatgpt_generator_presence_penalty' => 0.0,
            'module_chatgpt_generator_frequency_penalty' => 0.0,
            'module_chatgpt_generator_stop' => '',
            'module_chatgpt_generator_prompt' => 'Generate product description for seo store. For product {product_name}. Minimum 500 words.',
        );
        $this->model_setting_setting->editSetting('module_chatgpt_generator', $data);

        $this->addPermission($this->user->getGroupId(), 'access', 'tool/chatgpt_generator');
        $this->addPermission($this->user->getGroupId(), 'modify', 'tool/chatgpt_generator');

        $this->installTables();
    }

    public function installTables(){
        $this->db->query("CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "chatgpt_products` (
			`chatgpt_product_id` INT(11) NOT NULL AUTO_INCREMENT ,
			`product_id` INT(11) NOT NULL,
			PRIMARY KEY (`chatgpt_product_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $this->db->query("CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "chatgpt_products_bad` (
			`chatgpt_product_bad_id` INT(11) NOT NULL AUTO_INCREMENT ,
			`product_id` INT(11) NOT NULL,
			PRIMARY KEY (`chatgpt_product_bad_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }
}