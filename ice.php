<?php

class IceCast 
{
    var $server = "http://localhost:8000";
    var $stats_file = "/status.xsl";
    var $stream_info;
    
    function __construct() {    
        $this->stream_info = array();
    }

    function setUrl($url) {
        $this->server=$url;
    }

    private function fetch() {
        //create a new curl resource
        $ch = curl_init();

        //set url
        curl_setopt($ch,CURLOPT_URL,$this->server.$this->stats_file);

        //return as a string
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        //$output = our stauts.xsl file
        $output = curl_exec($ch);

        //close curl resource to free up system resources
        curl_close($ch);

        return $output;
    }

    function getStatus() 
    {
        $output=$this->fetch();

        $temp_array = array();

        $search_for = "<td\s[^>]*class=\"streamdata\">(.*)<\/td>";
        $search_td = array('<td class="streamdata">','</td>');
        
        preg_match_all("/<table\s[^>]*border=\"0\"\scellpadding=\"4\">(.*)<\/table>/siU",$output, $tables);
        
        $i = 0;
        foreach($tables[0] as $table)
        {
            $temp_array[$i] = array();
            preg_match_all("/$search_for/siU",$table,$matches);
            foreach($matches[0] as $match)
            {
                 $temp_array[$i][] = trim(str_replace($search_td,'',$match));
            }
            $i++;
        }
        
        $i = 0;      
        foreach($temp_array as $stream)
        {
            
            $this->stream_info[$i] = new RadioInfo();
            $this->stream_info[$i]->server = $this->server; 
            $this->stream_info[$i]->StreamTitle = $stream[0];
            $this->stream_info[$i]->StreamDescription = $stream[1];
            $this->stream_info[$i]->ContentType = $stream[2];
            $this->stream_info[$i]->Mountstarted = $stream[3];
            if(in_array("audio/aac", $stream))
            {
                $this->stream_info[$i]->CurrentListeners = $stream[4];
                $this->stream_info[$i]->PeakListeners = $stream[5];
                $this->stream_info[$i]->MaxListeners = $stream[6];
                $this->stream_info[$i]->StreamGenre = $stream[7];
                $this->stream_info[$i]->StreamURL = $stream[8];
                $this->stream_info[$i]->CurrentSong = $stream[9];
            }
            if(in_array("audio/mpeg", $stream))
            {
                $this->stream_info[$i]->Bitrate = $stream[4];
                $this->stream_info[$i]->CurrentListeners = $stream[5];
                $this->stream_info[$i]->PeakListeners = $stream[6];
                $this->stream_info[$i]->MaxListeners = $stream[7];
                $this->stream_info[$i]->StreamGenre = $stream[8];
                $this->stream_info[$i]->StreamURL = $stream[9];
                $this->stream_info[$i]->CurrentSong = $stream[10];
            } 
            if(in_array("application/ogg", $stream))
            {
                $this->stream_info[$i]->Quality= $stream[4];
                $this->stream_info[$i]->CurrentListeners = $stream[5];
                $this->stream_info[$i]->PeakListeners = $stream[6];
                $this->stream_info[$i]->MaxListeners = $stream[7];
                $this->stream_info[$i]->StreamGenre = $stream[8];
                $this->stream_info[$i]->StreamURL = $stream[9];
                $this->stream_info[$i]->CurrentSong = $stream[10];
            }  
            $i++;
        }
  
        return $this->stream_info;
    }

}

class RadioInfo
{
    public $server;
    public $StreamTitle;
    public $StreamDescription;
    public $ContentType;
    public $Mountstarted;
    public $CurrentListeners;
    public $PeakListeners;
    public $MaxListeners;
    public $StreamGenre;
    public $StreamURL;
    public $CurrentSong;
    public $Bitrate;
    public $Quality;
}
?>