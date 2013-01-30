<?php
  
class badge{
     function __construct($bid,$issued=false){
        global $DB,$USER, $CFG;
        if(!isset($this->badge)){
           if(!$this->badge = $DB->get_record('block_badger_badges',array('id'=>$bid))){
               print_error('nobadgeerror','block_badger');
           } 
        }     
        //record if this badge has been issued
        if($issued){
            $this->issued=$issued;
        }else{
            //if not issued will still be false
            $this->issued=$DB->get_record('block_badger_badges_issued',array('userid'=>$USER->id,'badgeid'=>$bid));
        }
        $this->width=40;
     }
     static function badgefromname($name){
        global $DB;
        return new badge($DB->get_field('block_badger_badges','id',array('name'=>$name))); 
     } 
     static function badgefromiid($iid){
        global $DB;
        $exists = $DB->get_record('block_badger_badges_issued',array('id'=>$iid));
        $badge = new badge($exists->badgeid,$exists); 
        return $badge;
     } 
     static function api(){
        return "http://beta.openbadges.org/issuer.js";
     } 
     function name(){
        return $this->badge->name; 
     }
     function uid(){
        if($this->badge->nickname){
            return $this->badge->nickname;
        }else{
            $blvl = $this->level(true); 
            return $this->badge->name . ($blvl?' '.$blvl:''); 
        }
     }
     function level($badge=false){
        return $badge?$this->badge->level:($this->issued?$this->badge->level:0); 
     }
     function family(){
        global $DB;
        return $DB->get_records('block_badger_badges',array('name'=>$this->name(),'collection'=>$this->badge->collection),'level'); 
     }
     function recipient(){
        global $DB;
        return fullname($DB->get_record('user',array('id'=>$this->issued->userid)));  
     }
     function issue($sid){
        global $DB;
        if(!$this->issued = $DB->get_record('block_badger_badges_issued', array('userid'=>$sid,'badgeid'=>$this->badge->id))){
            $this->issued->userid=$sid; 
            $this->issued->badgeid=$this->badge->id;
            $this->issued->claimed=date('Ymd');
            $this->issued->verified=date('Ymd');
            $this->issued->issued=date('Ymd');
            $this->issued->salt=md5(date("Ymdhis"));
            $this->issued->id=$DB->insert_record('block_badger_badges_issued',$this->issued);
        }
     }
     function details($image=false,$size=120){
        global $DB;
        $out .= html_writer::start_tag('table');
        $out .= html_writer::start_tag('tr'); 
        $out .= html_writer::tag('td',$image?$image:$this->image($size),array('style'=>'padding:20px;vertical-align:top;')); 
        $out .= html_writer::start_tag('td',array('width'=>'100%'));
        $out .= html_writer::tag('h3',get_string('badge','block_badger') .':') . html_writer::tag('p',$this->uid()); 
        if($this->badge->nickname){
            $out .= html_writer::tag('h3',get_string('name','block_badger').':') . html_writer::tag('p',$this->badge->name); 
        }
        if($this->badge->level){
            $out .= html_writer::tag('h3',get_string('level','block_badger').':') . html_writer::tag('p',$this->badge->level); 
        }
        if($this->badge->collection){
            $out .= html_writer::tag('h3',get_string('collection','block_badger').':') . html_writer::tag('p',$this->badge->collection); 
        }
        $out .= html_writer::tag('h3',get_string('description','block_badger').':') . html_writer::tag('p',$this->badge->description); 
        $out .= html_writer::tag('h3',get_string('criteria','block_badger').':') . html_writer::tag('p',$this->badge->criteria);
        $name = $DB->get_field('course','fullname',array('id'=>$this->badge->courseid));
        $out .= html_writer::tag('h3',get_string('definedin','block_badger').':') . html_writer::tag('a',$name,array('href'=>$CFG->wwwroot . '/course/view.php?id='.$this->badge->courseid));
        $out .= html_writer::end_tag('td');
        $out .= html_writer::end_tag('tr');
        $out .= html_writer::end_tag('table');
        return $out; 
     }
     function criteria(){
        $out = '<h3>Criteria:</h3>' .$this->badge->criteria;
        return $out; 
     }
     function assertion(){
        global $CFG,$DB;
        $email=$DB->get_field('user','email',array('id'=>$this->issued->userid));

        $hashed_email = hash('sha256', $email . $this->issued->salt);  
        return array(
            "recipient"=> 'sha256$' . $hashed_email,
            "salt"=> $this->issued->salt,
            "evidence"=> $CFG->wwwroot."/blocks/badger/report.php?type=evidence&badgeid={$this->badge->id}",
            "expires"=> "2099-12-31",
            "issued_on"=> date('Y-m-d'),
            "badge" =>array("version"=> "0.5.0",
                            "name"=> $this->badge->name,
                            "image"=> $this->badge->image,
                            "description"=> $this->badge->description,
                            "criteria"=> $CFG->wwwroot."/blocks/badger/report.php?type=criteria&badgeid={$this->badge->id}",
                            "issuer"=>array("origin"=> $CFG->wwwroot,
                                            "name"=> isset($CFG->block_badger_issuer_name)?$CFG->block_badger_issuer_name:get_string('issuername','block_badger'),
                                            "org"=> isset($CFG->block_badger_issuer_org)?$CFG->block_badger_issuer_org:get_string('issuername','block_badger'),
                                            "contact"=> isset($CFG->block_badger_issuer_email)?$CFG->block_badger_issuer_email:$CFG->noreplyaddress)
                                            )
            ) ;
     }
     function assertionurl(){
        global $CFG;                                                            
        return $CFG->wwwroot . '/blocks/badger/retrieve.php?id='.$this->issued->id; 
     }
     function image($size=0,$attr=array()){
        $size = $size?$size:$this->size; 
        if(!isset($attr['title'])){
            $attr['title']=$this->uid() . get_string('clickdetails','block_badger');
        }
        $attr['width']=$size.'px';
        if(!$this->issued->id){    
            $attr['src']=$CFG->wwwroot.'/blocks/badger/badges/missing.png';
        }else{
            $attr['src']=$this->badge->image;
        }
        return html_writer::tag('img','',$attr);
        
     }
     function render($size=0,$userid=0){ 
        return html_writer::tag('a',$this->image($size),array('href'=>'/blocks/badger/report.php?badgeid='.$this->badge->id)); 
     }
     function retire($state=1){
        global $DB;
        $this->badge->deleted=$state;
        $DB->update_record('block_badger_badges',$this->badge); 
     }
     function issuer(){
        global $CFG;
        return $CFG->wwwroot.'/blocks/badger/issue.php?badgeid='.$this->badge->id;
     }
     // how many of this badge has been issued?
     function issued(){
        global $DB;
        return $DB->get_field_sql('select count(t0.id) as freq from {block_badger_badges_issued} t0 inner join {block_badger_badges} t1 on t1.id=t0.badgeid where collection="'.$this->badge->collection . '" and level='.$this->badge->level. ' and name="'.$this->badge->name.'"'); 
     }
     function status($url){
        global $DB,$CFG;
        $c=$this->issued();
        $url->remove_params('delete','fulledit','edit','retire','unretire');
        if($this->badge->deleted){
           $url->param('unretire',$this->badge->id); 
           echo html_writer::tag('h3',get_string('retirement','block_badger'));
           echo html_writer::tag('p',get_string('previouslyawarded','block_badger',$c) . html_writer::tag('a','out of retirement',array('href'=>$url))); 
        }else{
           echo html_writer::tag('h3',get_string('claimingurl','block_badger'))  
                . html_writer::tag('pre',$this->issuer());
           if($this->badge->courserestrictions){
               $allowed=explode(',',$this->badge->courserestrictions);
               $allowed = array_combine($allowed, $allowed);
               unset($allowed[$this->badge->courseid]);
               if(count($allowed)>0) {
                    foreach ($allowed as $value) {
                       $name = $DB->get_field('course','fullname',array('id'=>$value));
                       $courses[] = html_writer::tag('a',$name,array('href'=>$CFG->wwwroot . '/course/view.php?id='.$value));
                    }
                    echo html_writer::tag('h3',get_string('alsoclaimablefrom','block_badger'));
                    echo html_writer::tag('p',implode(',',$courses));
               }
           }
           
           if($c){
               echo html_writer::tag('h3',get_string('alreadyawarded','block_badger',array('count'=>$c)));
               
               $issues = $DB->get_records_sql("select t1.id,firstname,lastname from {user} t0 inner join {block_badger_badges_issued} t1 on t0.id=t1.userid where badgeid={$this->badge->id}");
               if($issues){
                   $url->remove_params('retire');
                   foreach($issues as $k=>$v){
                       echo html_writer::start_tag('ul');
                       $url->param('revoke',-1*$k);
                       echo html_writer::tag('li',html_writer::tag('a',fullname($v),array(href=>$url)));
                       echo html_writer::end_tag('ul');
                   }
                   $url->remove_params('revoke');
               }
               
               echo html_writer::start_tag('ul');
               $url->param('retire',$this->badge->id);
               echo html_writer::tag('li',get_string('youmay',
                                                        'block_badger', 
                                                        array('action'=>html_writer::tag('a',get_string('retirebadge','block_badger'),array('href'=>$url)) )
                                            ));
               $url->remove_params('retire');
               $url->param('edit',$this->badge->id); 
               echo html_writer::tag('li',get_string('make',
                                                        'block_badger', 
                                                        array('action'=>html_writer::tag('a',
                                                                                            get_string('minorchanges','block_badger'),
                                                                                            array('href'=>$url)))));
               echo html_writer::end_tag('ul');
            }else{
               echo html_writer::tag('h3',get_string('notawarded','block_badger'));
               echo html_writer::start_tag('ul');
               $url->param('delete',$this->badge->id);
               echo html_writer::tag('li',get_string('youmay','block_badger',
                                                        array('action'=>html_writer::tag('a',get_string('deleteentirely','block_badger'),array('href'=>$url)))));
               $url->remove_params('delete');
               $url->param('fulledit',$this->badge->id); 
               echo html_writer::tag('li',html_writer::tag('a',get_string('makechanges','block_badger'),array('href'=>$url)));
               echo html_writer::end_tag('ul');
            }
        }
        echo html_writer::tag('hr',''); 
     }
     // produce a progress bar of appropriate % length
     function progress($cnt,$max){
        global $CFG;
        if($max){
            $pc= round($cnt*100/$max);
        }else{
            $pc=0;
        }
        return html_writer::tag('img','',array('border'=>1,'height'=>'14','src'=>$CFG->wwwroot . '/blocks/badger/images/graph0.gif','width'=>$pc.'%'));
     }
     
  }
?>