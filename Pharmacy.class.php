<?php
    /**
     * @Author: Ufuk OZDEMIR
     * @Created: 1.05.2022 20:02
     * @Web: https://www.ufukozdemir.website/
     * @Mail: ufuk.ozdemir1990@gmail.com
     * @Phone: +90 (532) 131 73 07
     */

    class Pharmacy
    {
        /**
         * District
         * @var
         */
        private $district;

        /**
         * Curl Data
         * @var bool|string
         */
        private $curlData;

        /**
         * JSON Data
         * @var array
         */
        private $data = array();

        /**
         * Pharmacy constructor.
         * @param $district
         */
        public function __construct($district)
        {
            $this->district = $district;
            $this->curlData = $this->curl('https://www.bulurum.com/nobetci-eczane/'.$this->convert($this->district).'/');
            $this->explodeData();
        }

        /**
         * Explode Data
         * @return void
         */
        private function explodeData()
        {
            if ($this->curlData) {

                preg_match_all('#<h1 class="UsefulTitlePharmacies">(.*?)</h1>#si', $this->curlData,$cur_title);
                preg_match_all('#<h2>(.*?)</h2>#si', $this->curlData,$cur_name);
                preg_match_all('#<div class="ResultAddr">(.*?)</div>#si', $this->curlData,$cur_address);
                preg_match_all('#<div class="DutyDay">(.*?)</div>#si', $this->curlData,$cur_date);
                preg_match_all("#<span class='firstTime'>(.*?)</span>#si", $this->curlData,$cur_time);
                preg_match_all('#<span class="spPhone">(.*?)</span>#si', $this->curlData,$cur_phone);

                $total = count($cur_address[1]);
                if ($total > 0) {
                    for ($i = 0; $i < $total; $i++) {
                        $explode_name = explode(' - ', $cur_name[1][$i]);
                        $name = trim(strip_tags(html_entity_decode($explode_name[0])));
                        $address = trim(strip_tags(html_entity_decode($cur_address[1][$i])));
                        $date = explode(' ', trim(strip_tags(html_entity_decode($cur_date[1][$i]))));
                        $time = trim(strip_tags(html_entity_decode(str_replace('-', ' - ', $cur_time[1][$i]))));

                        $this->data[$i]['title']    = $cur_title[1][0];
                        $this->data[$i]['district'] = $this->district;
                        $this->data[$i]['date']     = $date[1];
                        $this->data[$i]['day']      = $date[0];
                        $this->data[$i]['time']     = $time;
                        $this->data[$i]['name']     = $this->ucwordsTR($name);
                        $this->data[$i]['address']  = $address;
                        $this->data[$i]['phone']    = trim($cur_phone[1][$i]);
                        $this->data[$i]['maps']     = 'https://maps.google.com/maps?q='.urlencode($this->ucwordsTR($name));
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
        public function get($time = 60)
        {
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
        private function convert($string)
        {
            $tr = array('ş','Ş','ı','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç');
            $en = array('s','s','i','i','g','g','u','u','o','o','c','c');
            $string = str_replace($tr, $en, $string);
            $string = strtolower($string);
            $string = preg_replace('/&.+?;/', '', $string);
            $string = preg_replace('/[^%a-z\d _-]/', '', $string);
            $string = preg_replace('/\s+/', '-', $string);
            $string = preg_replace('|-+|', '-', $string);
            return trim($string, '-');
        }

        /**
         * Phone Convert
         * @param $string
         * @return string
         */
        public function format($string)
        {
            $str1 = substr($string, 0, 1);
            $str2 = substr($string, 1, 3);
            $str3 = substr($string, 4, 3);
            $str4 = substr($string, 7, 4);
            return $str1.' ('.$str2.') '.$str3.' '.$str4;
        }

        /**
         * Phone Clear
         * @param $string
         * @return string
         */
        public function clear($string)
        {
            $string = preg_replace("/\s+/", "", $string);
            $string = str_replace("(", "", $string);
            $string = str_replace(")", "", $string);
            return trim($string);
        }

        /**
         * Turkish Ucword
         * @param $string
         * @return string
         */
        public function ucwordsTR($string)
        {
            $result = '';
            $words = explode(" ", $string);
            foreach($words as $word) {
                $word_length = strlen($word);
                $first_character = mb_substr($word, 0, 1, 'UTF-8');

                if ($first_character == 'Ç' or $first_character == 'ç') $first_character = 'Ç';
                elseif ($first_character == 'Ğ' or $first_character == 'ğ') $first_character = 'Ğ';
                elseif ($first_character == 'I' or $first_character == 'ı') $first_character = 'I';
                elseif ($first_character == 'İ' or $first_character == 'i') $first_character = 'İ';
                elseif ($first_character == 'Ö' or $first_character == 'ö') $first_character = 'Ö';
                elseif ($first_character == 'Ş' or $first_character == 'ş') $first_character = 'Ş';
                elseif ($first_character == 'Ü' or $first_character == 'ü') $first_character = 'Ü';
                else $first_character = strtoupper($first_character);

                $digerleri = mb_substr($word, 1, $word_length , 'UTF-8');
                $result.= $first_character . $this->createLower($digerleri) . ' ';
            }
            return trim(str_replace('  ', ' ', $result));
        }

        /**
         * @param $string
         * @return string
         */
        public function createLower($string)
        {
            $string = str_replace('Ç', 'ç', $string);
            $string = str_replace('Ğ', 'ğ', $string);
            $string = str_replace('I', 'ı', $string);
            $string = str_replace('İ', 'i', $string);
            $string = str_replace('Ö', 'ö', $string);
            $string = str_replace('Ş', 'ş', $string);
            $string = str_replace('Ü', 'ü', $string);
            return strtolower($string);
        }

        /**
         * Data Extraction with Curl
         * @param $link
         * @return bool|string
         */
        private function curl($link)
        {
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
