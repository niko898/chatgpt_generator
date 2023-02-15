<?php
class ModelToolChatgptGenerator extends Model {
    public $debug = 0;

    public function __construct($registry)
    {
        parent::__construct($registry);
        ini_set('max_execution_time', 3000);

        if($this->config->get('module_chatgpt_generator_debug_status')){
            $this->debug = 1;
        }
    }

    public function startGeneration(){
        if(!$this->config->get('module_chatgpt_generator_status') || !$this->config->get('module_chatgpt_generator_prompt') || $this->config->get('module_chatgpt_generator_prompt') == ''){
            return false;
        }

        $this->load->model('localisation/language');
        $languages_data_raw = $this->model_localisation_language->getLanguages();
        $languages_data = array();
        foreach ($languages_data_raw as $language){
            $languages_data[$language['language_id']] = $language['name'];
        }

        $product_id = $this->getOneProduct();
        $this->log("Start work with product $product_id");

        while ($product_id){
            if(!$this->config->get('module_chatgpt_generator_status') || !$this->config->get('module_chatgpt_generator_prompt') || $this->config->get('module_chatgpt_generator_prompt') == ''){
                return false;
            }
            if($product_id && $product_id != 0){
                $languages = $this->config->get('module_chatgpt_generator_languages');
                if(!count($languages)){
                    $languages[] = $this->config->get('config_language_id');
                }

                $this->load->model('catalog/product');
                $product_data = $this->model_catalog_product->getProduct($product_id);
                $productDescriptions = $this->model_catalog_product->getProductDescriptions($product_id);


                foreach ($languages as $language_id){
                    $prompt = $this->config->get('module_chatgpt_generator_prompt');
                    if(isset($languages_data[$language_id])){
                        $prompt .= " In " . $languages_data[$language_id] . " language.";
                    }

                    $this->log("Use language $language_id for product $product_id");

                    $find = array(
                        '{product_name}',
                        '{product_model}',
                        '{product_sku}',
                        '{product_ean}'
                    );

                    if(!isset($productDescriptions[$language_id]['name'])){
                        $productDescriptions[$language_id]['name'] = '';
                    }

                    $replace = array(
                        'product_name' => $productDescriptions[$language_id]['name'],
                        'product_model'  => $product_data['model'],
                        'product_sku'   => $product_data['sku'],
                        'product_ean' => $product_data['ean'],
                    );

                    $prompt = preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $prompt)));

                    $result_ai = $this->generate($prompt);

                    if($result_ai){
                        if(isset($result_ai['choices'][0]['text'])){
                            $raw_text = trim($result_ai['choices'][0]['text']);
                            $this->updateProductDescription($product_id, $raw_text, $language_id);
                            $this->log("The product $product_id with lang $language_id - description updated");
                        }else{
                            $this->log("Process stopped because we got empty response text from chatGPT");
                            return false;
                        }
                    }else{
                        $this->log("Process stopped because we got error from chatGPT");
                        return false;
                    }
                }
            }else{
                break;
            }
            $product_id = $this->getOneProduct();
        }
        return true;
    }

    public function generate($prompt){
        $dTemperature = (float)$this->config->get('module_chatgpt_generator_temperature');
        $iMaxTokens = (int)$this->config->get('module_chatgpt_generator_max_tokens');
        $top_p = (float)$this->config->get('module_chatgpt_generator_top_p');;
        $frequency_penalty = (float)$this->config->get('module_chatgpt_generator_frequency_penalty');
        $presence_penalty = (float)$this->config->get('module_chatgpt_generator_presence_penalty');
        $OPENAI_API_KEY = $this->config->get('module_chatgpt_generator_api_key');
        $sModel = $this->config->get('module_chatgpt_generator_model');
        $headers  = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $OPENAI_API_KEY . ''
        ];

        $stop = "\n";
        if($this->config->get('module_chatgpt_generator_stop')){
            $stop = explode(',', $this->config->get('module_chatgpt_generator_stop'));
        }
        $postData = [
            'model' => $sModel,
            'prompt' => str_replace('"', '', $prompt),
            'temperature' => $dTemperature,
            'max_tokens' => $iMaxTokens,
            'top_p' => $top_p,
            'frequency_penalty' => $frequency_penalty,
            'presence_penalty' => $presence_penalty,
            'stop' => json_encode($stop),
        ];

        $result = $this->request('https://api.openai.com/v1/completions', $headers, $postData);
        return $result;
    }


    public function request($url, $header = array(), $data = array()){
        if(!$header){
            $header = array('Content-Type:application/x-www-form-urlencoded');
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $code = (int) $code;
        $errors = array(
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        );
        if ($code < 200 && $code > 204) {
            $this->log("We got some error $code: " . $errors[$code]);
            return false;
        }
        $response = json_decode($out, true);
        $this->log("Request to chatGPT - $url : " . print_r($data, true));
        $this->log("Answer from chatGPT - $url - [$code]: " . print_r($response, true));
        return $response;
    }

    public function getProductDescriptionEmpty(){
        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_description WHERE description = '' AND language_id='" . (int)$this->config->get('config_language_id') . "'");
        if ($query->num_rows) {
            return $query->rows;
        }
        return false;
    }

    public function addProduct($product_id){
        if(!$this->checkProduct($product_id)){
            $this->db->query("INSERT INTO  " . DB_PREFIX . "chatgpt_products SET product_id = ' " . (int) $product_id . "'");
        }
    }

    public function checkProduct($product_id){
        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "chatgpt_products WHERE product_id = ' " . (int) $product_id . "'");
        if ($query->num_rows) {
            return true;
        }
        return false;
    }

    public function getProducts(){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "chatgpt_products ");
        if ($query->num_rows) {
            return $query->rows;
        }
        return false;
    }

    public function getOneProduct(){
        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "chatgpt_products LIMIT 1");
        if ($query->num_rows) {
            $this->deleteProduct($query->row['product_id']);
            return $query->row['product_id'];
        }
        return false;
    }

    public function deleteProduct($product_id){
        $this->db->query("DELETE FROM " . DB_PREFIX . "chatgpt_products WHERE product_id = '" . (int) $product_id . "'");
    }

    public function addProductBad($product_id){
        if(!$this->checkBadProduct($product_id)){
            $this->db->query("INSERT INTO  " . DB_PREFIX . "chatgpt_products_bad SET product_id = ' " . (int) $product_id . "'");
        }
    }

    public function checkBadProduct($product_id){
        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "chatgpt_products_bad WHERE product_id = ' " . (int) $product_id . "'");
        if ($query->num_rows) {
            return true;
        }
        return false;
    }

    public function getProductsBad(){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "chatgpt_products_bad ");
        if ($query->num_rows) {
            return $query->rows;
        }
        return false;
    }

    public function getOneProductBad(){
        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "chatgpt_products_bad LIMIT 1");
        if ($query->num_rows) {
            $this->deleteProduct($query->row['product_id']);
            return $query->row['product_id'];
        }
        return false;
    }

    public function deleteProductBad($product_id){
        $this->db->query("DELETE FROM " . DB_PREFIX . "chatgpt_products_bad WHERE product_id = '" . (int) $product_id . "'");
    }

    public function log($message){
        if(!$this->debug){
            return;
        }
        if (file_exists(DIR_LOGS . 'chatgpt_generator.log') && filesize(DIR_LOGS . 'chatgpt_generator.log') >= 100 * 1024 * 1024) {
            unlink(DIR_LOGS . 'chatgpt_generator.log');
        }
        if($this->debug){
            file_put_contents(DIR_LOGS . 'chatgpt_generator.log', date("Y-m-d H:i:s - ") . ": " . $message . "\r\n", FILE_APPEND);
        }
    }

    public function updateProductDescription($product_id, $description, $language_id = 0){
        if(!$language_id){
            $language_id = $this->config->get('config_language_id');
        }
        $this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($description) . "' WHERE product_id = '" . (int)$product_id  . "' AND language_id='" . (int)$language_id . "'");
    }
}