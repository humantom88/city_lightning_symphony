<?php

namespace MManager\MControlBundle\Classes;

set_include_path(get_include_path() . '\\'. 'Classes');
include "PHPExcel.php";
include 'PHPExcel/IOFactory.php';
include 'PHPExcel/Reader/Excel5.php';

class ExcelReader {
    private $inputFilename = "";
    private $inputFilepath;
    private $objectReader = "";
    private $objectPHPExcel = "";
    
    public function __construct($inputFilename = "", $inputFilepath = "") 
    {
        $this->setInputFilename($inputFilename);
        $this->setInputFilepath($inputFilepath);
        $this->setObjectReader();
        $this->setObjectPhpExcel();
    }
    
    public function getInputFilename ()
    {
        return $this->inputFilename;
    }
    
    public function setInputFilename ($inputFilename)
    {
        $this->inputFilename = $inputFilename;
    }
    
    public function getInputFilepath ()
    {
        return $this->inputFilepath;
    }
    
    public function setInputFilepath ($inputFilepath)
    {
        $this->inputFilepath = $inputFilepath;
    }
    
    public function setObjectReader() 
    {
        $this->objectReader = new PHPExcel_Reader_Excel5();
    }
    
    public function getObjectReader()
    {
        return $this->objectReader;
    }
    
    public function setObjectPhpExcel()
    {
        if (isset($this->objectReader)) {
            $this->objectPHPExcel = $this->objectReader->load($this->inputFilename);
        } else {
            $this->setObjectReader();
            $this->objectPHPExcel = $this->objectReader->load($this->inputFilename);
        }
    }
    
    public function getSheetData ()
    {
        var_dump($this->objectPHPExcel);
        return $this->objectPHPExcel->getActiveSheet()->toArray(null,true,true,true);
    }
}