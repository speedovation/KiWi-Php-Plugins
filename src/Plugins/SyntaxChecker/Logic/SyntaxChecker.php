<?php

namespace KiWi\Plugins\SyntaxChecker\Logic;

use PhpParser\Error;
use PhpParser\ParserFactory;

class SyntaxChecker
{
    
    protected $app;
    
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    
    
    function checkSyntaxPhp($code,$filename)
    {
        
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        
        try
        {
            //print $code. "  \n\n";
            $stmts = $parser->parse($code);
            // $stmts is an array of statement nodes
            return "";
        }
        catch (PhpParser\Error $e)
        {
            
        /*$errors = $parser->getErrors();

        foreach ($errors as $error) 
        {
            // $error is an ordinary PhpParser\Error
           // echo "PParse Error: ". $e->getMessage();
            
            //if( $error->hasColumnInfo() ) 
            {
                echo $error->getRawMessage() . 
                    ' from ' . $error->getStartLine() . 
                    //':' . $error->getStartColumn($code) . 
                    ' to ' . $error->getEndLine() ;//. 
                    //':' . $error->getEndColumn($code);
            }
        
            
            }*/
            
            
        /*if( $e->hasColumnInfo() ) 
        {
            echo $e->getRawMessage() . 
                ' from ' . $e->getStartLine() . 
                ':' . $e->getStartColumn($code) . 
                ' to ' . $e->getEndLine() . 
                ':' . $e->getEndColumn($code);
            }*/
            
            return $e;
            
            
            return "Parse Error: ". $e->getMessage().
            " - S: ". $e->getStartLine().
            " - E: ". $e->getEndLine();
        }
        
    }
    
    function checkSyntax($json)
    {
        //bool runkit_lint ( string $code )
        
        //runkit_lint();
        
        $json = str_replace("'","",$json);
        $json = str_replace("\\","",$json);
        $v = json_decode($json);
        
        
        echo "\n\n";
        
        $connection = \Tivoka\Client::connect(array('host' => '127.0.0.1', 'port' => 9040));
        
        $request = $connection->sendRequest('text',array('file_name'=> $v->file_path ));
        
        if(!$request->isError())
        {
            //$r = runkit_lint( $request->result );
            $code = $request->result;
            
            $r = $this->checkSyntaxPhp($code,$v->file_path);
            
            
            if(!empty($r) )
            {
                echo "Inside Error";
                
                //Call API to mark error spot on editor
                $connection = \Tivoka\Client::connect(array('host' => '127.0.0.1', 'port' => 9040));
                
                $request = $connection->sendRequest('setMarkers',array('start' =>  $e->getStartLine(),
                'end'  => $e->getEndLine(),
                'file_name' => $filename
                ));
                
                echo "Calling finised";
            }
            
            echo "R: ".$r ."\n\n";
        }
        else
        {
            print 'Error '.$request->error.': '.$request->errorMessage;
            var_dump($request->errorData);
        }
        
        //echo "\n\nDone: Checking Syntax of PHP File\n\n";
        
        
        
    }
    
    
}
