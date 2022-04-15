<?php
    /**
     * 	@author: Ufuk OZDEMIR
     * 	@mail: ufuk.ozdemir1990@gmail.com
     * 	@website: ufukozdemir.website
     */

    class Pharmacy {

        /**
         * District
         * @var
         */
        private $district;

        /**
         * Curl Data
         * @var bool|string
         */
        private $curl_data;

        /**
         * JSON Data
         * @var array
         */
        private $data = array();

        /**
         * Pharmacy constructor.
         * @param $district
         */
        public function __construct($district){
            $this->district = $district;
            $this->curl_data = $this->curl('https://www.bulurum.com/nobetci-eczane/'.$this->convert($this->district).'/');
            $this->explode_data();
        }

        /**
         * Explode Data
         * @return void
         */
        private function explode_data(){
            if ($this->curl_data) {

                preg_match_all('#<h1 class="UsefulTitlePharmacies">(.*?)</h1>#si', $this->curl_data,$cur_title);
                preg_match_all('#<h2>(.*?)</h2>#si', $this->curl_data,$cur_name);
                preg_match_all('#<div class="ResultAddr">(.*?)</div>#si', $this->curl_data,$cur_address);
                preg_match_all('#<div class="DutyDay">(.*?)</div>#si', $this->curl_data,$cur_date);
                preg_match_all("#<span class='firstTime'>(.*?)</span>#si", $this->curl_data,$cur_time);
                preg_match_all('#<span class="spPhone">(.*?)</span>#si', $this->curl_data,$cur_phone);

                $total = count($cur_address[1]);
                if ($total > 0) {
                    for ($i = 0; $i < $total; $i++) {
                        $explode_name = explode(' - ', $cur_name[1][$i]);
                        $name = trim(strip_tags(html_entity_decode($explode_name[0])));
                        $address = trim(strip_tags(html_entity_decode($cur_address[1][$i])));
                        $date = explode(' ', trim(strip_tags(html_entity_decode($cur_date[1][$i]))));

                        $time = trim(strip_tags(html_entity_decode(str_replace('-', ' - ', $cur_time[1][$i]))));

                        $this->data[$i]['title']    = $cur_title[1][0];
                        $this->data[$i]['district']      = $this->district;
                        $this->data[$i]['date']     = $date[1];
                        $this->data[$i]['day']       = $date[0];
                        $this->data[$i]['time']      = $time;
                        $this->data[$i]['name']       = $name;
                        $this->data[$i]['address']     = $address;
                        $this->data[$i]['phone']   = trim($cur_phone[1][$i]);
                        $this->data[$i]['maps']      = 'https://maps.google.com/maps?q='.urlencode($address);
                    }
                } else {
                    $this->data = NULL;
                }

            } else {
                $this->data = NULL;
            }
        }

        /**
         * Data Fetch with Caching
         * @param $time
         * @return mixed
         */
        public function get($time = 60) {
            $cacheName 	= 'pharmacy.json';
            $cacheAge 	= $time * 60;

            // Retrieve new data if file does not exist or cache has expired indefinitely
            if (!file_exists($cacheName) || time() - $cacheAge > filemtime($cacheName)) {
                file_put_contents($cacheName, json_encode($this->data));
            }

            // If the county in the cache file and the county in the construct do not match, get the new data
            $data = json_decode(file_get_contents($cacheName));
            if ($data[0]->district !== $this->district) {
                file_put_contents($cacheName, json_encode($this->data));
            }

            return json_decode(file_get_contents($cacheName));
        }

        /**
         * String Convert
         * @param $string
         * @return string
         */
        private function convert($string) {
            $tr = array('ş','Ş','ı','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç');
            $en = array('s','s','i','i','g','g','u','u','o','o','c','c');
            $string = str_replace($tr, $en, $string);
            $string = strtolower($string);
            $string = preg_replace('/&.+?;/', '', $string);
            $string = preg_replace('/[^%a-z0-9 _-]/', '', $string);
            $string = preg_replace('/\s+/', '-', $string);
            $string = preg_replace('|-+|', '-', $string);
            return trim($string, '-');
        }

        /**
         * Data Extraction with Curl
         * @param $link
         * @return bool|string
         */
        private function curl($link){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $link);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $source = curl_exec($ch);
            curl_close($ch);
            return $source;
        }

    }