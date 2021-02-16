<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    
    public function __construct(  )
    {
        $this->middleware('owner.auth:owner');
    }
    public function index()
    
    {
        /*
         * $amount = '12345.67';

                $formatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY);
                echo 'UK: ', $formatter->formatCurrency($amount, 'EUR'), PHP_EOL;
                
                $formatter = new NumberFormatter('de_DE',  NumberFormatter::CURRENCY);
                echo 'DE: ', $formatter->formatCurrency($amount, 'EUR'), PHP_EOL;
        
        */
        //dd( locale_get_default());
    
//        setlocale(LC_ALL,"US");
//        $locale_info = localeconv();
//       // print_r($locale_info);
//
//        $formatter = new \NumberFormatter( locale_get_default() ,  \NumberFormatter::CURRENCY);
//        $symbol =   $formatter->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
//        //dd($symbol);
       // dd(  $formatter->formatCurrency(5666, 'USD'), locale_get_default(),$formatter->getSymbol(\NumberFormatter::CURRENCY_SYMBOL),$locale_info);
        return view ('owner.home');
    }
    
    public function make_backup(  )
    {
        return view ('owner.backup.backup');
    }
    
    public function backup( Request $request )
    {
        $folders = [
            'C:\wamp\www\seifex\resources',
            'C:\wamp\www\seifex\routes',
            'C:\wamp\www\seifex\database',
            'C:\wamp\www\seifex\config',
            'C:\wamp\www\seifex\app'
       ];
        $full_app    = ['C:\wamp\www\seifex']  ;


        /*
         *  $folders = [
            'C:\Users\Marcel Kolarcik\code\seifex\resources',
            'C:\Users\Marcel Kolarcik\code\seifex\routes',
            'C:\Users\Marcel Kolarcik\code\seifex\database',
            'C:\Users\Marcel Kolarcik\code\seifex\config',
            'C:\Users\Marcel Kolarcik\code\seifex\app'
       ];
        $full_app    = ['\Users\Marcel Kolarcik\code\seifex']  ;*/


        $destination    =   'K:\Code\SEIFEX BACKUP';
        
        if($request->full_backup == 'on')
        {
            $full_backup = '_full_';
            $folders = $full_app;
            ini_set('max_execution_time', 900);
        }
        else
        {
            $full_backup = '_';
        }
       
       if  (!$request->backup_name)
        $folder_name    =  date('d-m-Y-D-U').$full_backup;
      else
        $folder_name    =   date('d-m-Y-D-U').$full_backup.str_replace(' ','_',$request->backup_name);
      
        mkdir($destination.'\\'.$folder_name);
        
        foreach($folders as $folder)
        {
            $resource_folder    =   explode('\\',$folder)[sizeof(explode('\\',$folder)) -1 ];
            mkdir($destination.'\\'.$folder_name.'\\'.$resource_folder);
           
           $this->xcopy($folder,$destination.'\\'.$folder_name.'\\'.$resource_folder,0755);
        }
//        try {
//            // run your code here
//
//        }
//        catch (exception $e) {
//            //code to handle the exception
//
//        }
//        finally {
//            //optional code that always runs
//
//        }
        
        
        $href   = 'file:///'.  $destination.'\\'.$folder_name;
        $path    ='file:///'.  $destination.'\\'.$folder_name;
        $files = array_diff(scandir($path), array('.', '..'));
       
       return back()->with(
        ['href'=> $href,'files'=>$files]
       );
    }
    
    /**
     * Copy a file, or recursively copy a folder and its contents
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @param       int      $permissions New folder creation permissions
     * @return      bool     Returns true on success, false on failure
     */
   private function xcopy($source, $dest, $permissions = 0755)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }
        
        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }
        
        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }
        
        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            
            // Deep copy directories
          $this->xcopy("$source/$entry", "$dest/$entry", $permissions);
        }
        
        // Clean up
        $dir->close();
        return true;
    }
}
