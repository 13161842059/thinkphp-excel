<?php
namespace Home\Controller;
class ExcelController extends CommonController {

	//花名册数据导入
    public function index(){
        $this->display();
    }
    
    //考试试题数据导入
    public function subject(){
        $this->display();
    }


    //导入
    public function eximport(){
    	set_time_limit(0);
    	ini_set('memory_limit','-1');
    	ini_set('max_execution_time', '0');
    	 
    	$upload = new \Think\Upload();
    	$upload->maxSize   =     3145728 ;
    	$upload->exts      =     array('xls', 'csv', 'xlsx');
    	$upload->rootPath  =      '../../Public';
    	$upload->savePath  =      '/excel/';
    	$info   =   $upload->upload();
    
    
    
    	if(!$info){
    		$this->error($upload->getError());
    	}else{
    		$filename='../../Public'.$info['excel']['savepath'].$info['excel']['savename'];
    		import("Org.Yufan.ExcelReader");
    		$ExcelReader=new \ExcelReader();
    		$excel_arr=$ExcelReader->reader_excel($filename);
    		echo "<pre>";
    		print_r($excel_arr);
    		exit();
    	}
    }





    
    //试题导入
    public function subjectImport(){
    	set_time_limit(0);
        $upload = new \Think\Upload();
        $upload->maxSize   =     3145728 ;
        $upload->exts      =     array('xls', 'csv', 'xlsx');
        $upload->rootPath  =      '../../Public';
        $upload->savePath  =      '/excel/';
        $info   =   $upload->upload();
    
        if(!$info){
            $this->error($upload->getError());
        }
        
        $filename='../../Public'.$info['excel']['savepath'].$info['excel']['savename'];
        import("Org.Yufan.ExcelReader");
        $ExcelReader=new \ExcelReader();
        $excel_arr=$ExcelReader->reader_excel($filename);
        echo "<pre>";
        print_r($excel_arr);
        exit;
        
        //数据初始化
        $subjects = array();
        //实例化数据模型
        $contract_model = D("ContractNature");
        $department_model = D("Department");
        //实例化对应的数据表
        $question_bank = M("question_bank");
        
        $subject_type = $contract_model->getSubjectType();

        foreach ($excel_arr as $key => $value) {
            sleep(0.5);
            $subjects['subject_type'] = isset($value[1])?array_search($value[1],$subject_type):NULL;
            $subjects['subject_answer1'] = trim($value[2]);
            $subjects['subject_answer2'] = trim($value[3]);
            $subjects['subject_answer3'] = isset($value[4])?trim($value[4]):NULL;
            $subjects['subject_answer4'] = isset($value[5])?trim($value[5]):NULL;
            $subjects['answer_num'] = trim($value[6]);
            $subjects['correct_answer'] = trim($value[7]);
            $subjects['subject_title'] = trim($value[8]);
            $subjects['subject_desc'] = trim($value[9]);

            $subjects['department_id'] = 1;
            
            $subjects['class_type'] = 1; //题库类型：1=政策保障班，2=第一阶段，3=第二阶段，4=第三阶段
            $subjects['create_time'] = date('Y-m-d H:i:s',time());

            //考试试题导入
            $result = $question_bank->data($subjects)->add();
            if($result){
                echo $value[1].$value[0].'#导入成功<br/>';
                echo '------------------------<br/>';
                $insert_count[] = $result;
            }
        }

        echo '成功导入'.count($insert_count).'条数据';
        exit();
        
    }

    
    

    //导出
    public function export(){
    	import("Org.Yufan.Excel");
    	$list = M('user')->select();
    	$row=array();
    	$row[0]=array('序号','用户id','用户名','金额','时间');
    	$i=1;
    	foreach($list as $v){
    	        $row[$i]['i'] = $i;
    	        $row[$i]['uid'] = $v['id'];
    	        $row[$i]['username'] = $v['username'];
    	        $row[$i]['money'] = $v['money'];
    	        $row[$i]['time'] = date("Y-m-d H:i:s",$v['time']);
    	        $i++;
    	}
    	echo "<pre>";
    	print_r($row);exit();
    	$xls = new \Excel_XML('UTF-8', false, 'datalist');
    	$xls->addArray($row);
    	$filename = date('Y-m-d').'导出数据';
    	$xls->generateXML($filename);
    }


}