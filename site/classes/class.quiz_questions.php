<?php

class QuizQuestions extends Settings {

    public static function getRecord(int $id) {
        global $db;
        global $lng;
        global $quiz_questions_table;

        $row = $db->getRow("SELECT 
                                qq.*, 
                                qq.name_{$lng} AS name
                            FROM
                                " . $quiz_questions_table . " AS qq
                            WHERE 
                                qq.id = " . $id); safeCheck($row);
        return $row;
    }
    
    public static function getOptionsRecord(int $id) {
        global $db;
        global $lng;
        global $quiz_questions_options_table;

        $row = $db->getRow("SELECT 
                                qqo.*, 
                                qqo.option_text_{$lng} AS option_text
                            FROM
                                " . $quiz_questions_options_table . " AS qqo
                            WHERE 
                                qqo.id = " . $id); safeCheck($row);
        return $row;
    }

    public function getQuizQuestions($activeOnly = 0) {
        global $db;
        global $lng;
        global $quiz_questions_table;
        
        if ($activeOnly) {
            $activeOnlySQL = " AND active = 1 ";
        }
        
        $items = $db->getAll("SELECT
                                *,
                                name_{$lng} AS name
                            FROM
                                ".$quiz_questions_table."
                            WHERE
                                edate = 0
                            {$activeOnlySQL}
                            ORDER BY pos"); safeCheck($items);
        foreach ($items as $k => $v) {
            $items[$k]["options"] = $this->getQuestionOptions($v["id"]);
        }
        
        return $items;
    }
    
    public function getQuizQuestionsByCategory($quiz_category_id, $returnType = 3) {
        global $db;
        global $lng;
        global $quiz_questions_table;
        
        $items = $db->getAll("SELECT
                                *,
                                name_{$lng} AS name
                            FROM
                                ".$quiz_questions_table."
                            WHERE
                                edate = 0
                            AND active = 1
                            AND quiz_category_id = {$quiz_category_id}
                            ORDER BY pos"); safeCheck($items);
        foreach ($items as $k => $v) {
            $items[$k]["options"] = $this->getQuestionOptions($v["id"]);
        }
        
        if($returnType == 3){
            echo json_encode($items);
            die();
        }
        
        return $items;
    }
    
    public function getQuestionOptions(int $question_id) {
        global $db;
        global $lng;
        global $quiz_questions_options_table;

        $res = $db->getAll("SELECT 
                                qo.*, 
                                qo.option_text_{$lng} AS option_text
                            FROM
                                " . $quiz_questions_options_table . " AS qo
                            WHERE 
                                qo.question_id = {$question_id}
                            AND qo.edate = 0
                            ORDER BY qo.pos
                            "); safeCheck($res);
        return $res;
    }
    
    public function getPage() {
        global $sm;
        
        $items = $this->getQuizQuestions(1);
        $sm->assign("items", $items);
        
        $age_groups = AgeGroups::getAgeGroups(1);
        $sm->assign("age_groups", $age_groups);
        
        $sm->display("quiz.html");
    }
    
    public function quizProceed(FilteredMap $params) {
        global $db;
        global $user;
        global $quiz_requests_table;
        global $quiz_requests_answers_table;
        global $quizEmail;
        global $emails_test;
        
        if($params->has("questions")){
        	
            $email = $params->getString("email");
            $age_id = $params->getInt("age");
            $sex = $params->getInt("sex");
            $quiz_category_id = $params->getInt("quiz_category_id");
            
            $fieldsRequets = array(
                'user_id'           => isset($user["id"]) ? $user["id"] : 0,
                'email'             => $email,
                'age'               => $age_id,
                'sex'               => $sex,
                'quiz_category_id'  => $quiz_category_id,
                'postdate'          => time(),
                'ip'                => $_SERVER["REMOTE_ADDR"]
            );
            $res = $db->autoExecute($quiz_requests_table, $fieldsRequets, DB_AUTOQUERY_INSERT); safeCheck($res);
            $request_id = mysqli_insert_id($db->connection);
            
            $age = AgeGroups::getRecord($age_id);
            $quiz_category = QuizCategories::getRecord($quiz_category_id);
            
            $message_text = "<h1>Quiz Request #".$request_id."</h1>";
            $message_text .= "<p>Email: ".$email."</p>";
            $message_text .= "<p>Age: ".$age["name"]."</p>";
            $sexText = $sex == 1 ? "Male" : "Female";
            $message_text .= "<p>Sex: ".$sexText."</p>";
            $message_text .= "<p>Category: ".$quiz_category["name"]."</p>";
            
            $answers = $params->get("questions");
            foreach ($answers as $question_id => $option_id) {
            	if(is_array($option_id)){
                $question = self::getRecord($question_id);
                $addText = '';
            		foreach($option_id as $multi => $multi_id){
	                $questionOption = self::getOptionsRecord($multi_id);
	                
	                $addText .= $questionOption["option_text"].", ";
	                
	                
	                $fieldsRequetAnswers = array(
	                    'request_id'    => $request_id,
	                    'question_id'   => $question_id,
	                    'question_name'   => $question["name"],
	                    'option_id'     => $multi_id,
	                    'option_name'     => $questionOption["option_text"],
	                    'postdate'      => time(),
	                    'ip'            => $_SERVER["REMOTE_ADDR"]
	                );
	                $res = $db->autoExecute($quiz_requests_answers_table, $fieldsRequetAnswers, DB_AUTOQUERY_INSERT); safeCheck($res);
            		}
            		$message_text .= "<p>".$question["name"].":<br>".$addText."</p>";
            	}
            	else{
                $question = self::getRecord($question_id);
                $questionOption = self::getOptionsRecord($option_id);
                
                $message_text .= "<p>".$question["name"].":<br>".$questionOption["option_text"]."</p>";
                
                $fieldsRequetAnswers = array(
                    'request_id'    => $request_id,
                    'question_id'   => $question_id,
                    'question_name'   => $question["name"],
                    'option_id'     => $option_id,
                    'option_name'     => $questionOption["option_text"],
                    'postdate'      => time(),
                    'ip'            => $_SERVER["REMOTE_ADDR"]
                );
                $res = $db->autoExecute($quiz_requests_answers_table, $fieldsRequetAnswers, DB_AUTOQUERY_INSERT); safeCheck($res);
              }
            }
            mailSender($quizEmail, "Quiz Request #".$request_id, $message_text);
            foreach ($emails_test as $email) {
                mailSender($email, "Quiz Request #".$request_id, $message_text);
            }
            header("Location: /messages/202");
        }else{
            header("Location: /messages/405");
        }
    }

}
