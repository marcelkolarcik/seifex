<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\cRepository;
use Illuminate\Support\Facades\DB;

class TranslationMakerController extends Controller
{
    public $world;
    public function __construct( cRepository $world )
    {
        $this->world =  $world;
    }
    
    public function load_english()
    {
        $translated=file('C:\wamp\www\seifex\resources\lang\slovak.txt');
        $en =   json_decode( file_get_contents('C:\wamp\www\seifex\resources\lang\en.json'),true);
        // To check the number of lines
        //echo count($translated).'<br>';
        $new_translation = [];
        if(sizeof($en) === count($translated))
        {
            foreach($translated as $line)
            {
                /*echo $line.'<br>';*/
                $new_translation[]  =   $line;
            }
        }
        else
        {
            dd(' Incorect translation, line numbers do not match !');
        }
        
        
        $line_number        =   0;
        $translated_array   =   [];
        $translated_json    =   '';
        
        foreach($en as $english =>  $translation)
        {
           
            $translated_array[$english] = $new_translation[$line_number];
            $line_number++;
        }
        $translated_json    =   json_encode($translated_array,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        dd($translated_json);
        
        
    }
    
   
}
