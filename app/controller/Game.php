<?php
// mergearrays & diffarrays @LuaHelper
namespace controller;

use \Config;
use \Helper;
use \RunCycle;
use \Luaobjects;
use \Security;
use \Database;

class Game extends AbstractController {

	// protected $loginRequired = true;

	public function _get() {
		if (isset($this->data['a'])) {
			switch($this->data['a']) {
				case 'refresh':
					$this->result = RunCycle::printobjects();
					break;
				case 'keypress':
					if (isset($this->data['key'])) {
						RunCycle::keypress($this->data['key']);
					} else {
						$this->result = 'missing key';
					}
					break;
				case 'uhandlers':
					RunCycle::updatehandlers();
					break;
				case 'luadebug':
					$this->result = RunCycle::printdebugs();
					break;
				case 'luaerrors':
					$this->result = RunCycle::printerrors();
					break;
				case 'rundotlog':
					$this->result = $this->dumpRunDotLog();
					break;
				case 'update':
					RunCycle::run();
					break;
				case 'start':
					$this->start();
					break;
				case 'stop':
					$this->stop();
					break;
                case 'setavatar':
                    if (isset($this->data['id'])){
                        $this->setavatar($this->data['id']);
                    } else {
                        $this->result = 'missing id';
                    }
                    break;
                case 'runandprint':
                    $this->runAndPrint();
                    break;
				default:
					$this->result = 'unknown action';
					break;
			}
		} else {
			if (Security::isLoggedIn()) {
				$this->view->appendData('avatarid', Security::getLoggedInUser()->getAvatar());
			}
            // $this->prodstart();
		}
	}

    private function runAndPrint(){
					RunCycle::updatehandlers();
                    RunCycle::run();
					$this->result = RunCycle::printobjects();
    }

    private function setavatar($id){
        $userid = Security::getLoggedInUser()->getId();
        if ($userid){
            Database::getDb('user')[$userid]->update(array("avatar" => $id));
        }
    }

	public static function prodstart(){
		$slivers = $this->getpid();
		if (!empty($slivers))
			$running = $this->isRunning($slivers);
		else
			$running = false;
		if (!$running){
			$parts = array(
				APPLICATION_PATH,
				Config::get('app.path.misc')
			);
			$outputfile = "run.log";
			array_push($parts, $outputfile);
			$pathOutputfile = Helper::makePathFromParts($parts);
			array_pop($parts);
			$cmd = "run.sh";
			array_push($parts, $cmd);
			$pathCmd = Helper::makePathFromParts($parts);
			$slivers = $this->startCmdBg($pathCmd, $pathOutputfile);
		}
	}

	public function debugstart(){
			$parts = array(
				APPLICATION_PATH,
				Config::get('app.path.misc')
			);
			$outputfile = "run.log";
			array_push($parts, $outputfile);
			$pathOutputfile = Helper::makePathFromParts($parts);
			array_pop($parts);
			$cmd = "run.sh";
			array_push($parts, $cmd);
			$pathCmd = Helper::makePathFromParts($parts);
			$message = $this->debugstartCmdBg($pathCmd, $pathOutputfile);
            return $message;
	}

	public function start(){
		$slivers = $this->getpid();
		if (!empty($slivers))
			$running = $this->isRunning($slivers);
		else
			$running = false;
		if (!$running){
			$parts = array(
				APPLICATION_PATH,
				Config::get('app.path.misc')
			);
			$outputfile = "run.log";
			array_push($parts, $outputfile);
			$pathOutputfile = Helper::makePathFromParts($parts);
			array_pop($parts);
			$cmd = "run.sh";
			array_push($parts, $cmd);
			$pathCmd = Helper::makePathFromParts($parts);

			$slivers = $this->startCmdBg($pathCmd, $pathOutputfile);
			if (empty($slivers)) {
				echo 'process already finished!<br />';
				echo '<pre>';
				echo file_get_contents($pathOutputfile);
                echo "\n\n";
                echo $this->debugstart();
				echo '</pre>';
			} else {
				$this->setpid($slivers);
				echo "started; pid: ".$slivers['pid'];
			}
		} else {
			echo "already started; pid: ".$slivers['pid'];
		}
	}

	public function stop(){
		$slivers = $this->getpid();
		if (!empty($slivers))
			$running = $this->isRunning($slivers);
		else
			$running = false;
		if (!$running){
			echo "wasn't running (anyway)";
		} else {
			echo shell_exec("kill -9 ".$slivers['pid']);
			echo shell_exec("kill -9 ".strval(intval($slivers['pid'])+1));
			$parts = array(
				APPLICATION_PATH,
				Config::get('app.path.misc'),
				'pid.txt'
			);
			echo shell_exec("rm ".Helper::makePathFromParts($parts));
			echo "stopped";
		}
	}

	private function setpid($slivers){
		$parts = array(
			APPLICATION_PATH,
			Config::get('app.path.misc'),
			'pid.txt'
		);
		$f = fopen(Helper::makePathFromParts($parts), "c");
		$output = $slivers['pid']."\n".$slivers['cmd']."\n".$slivers['output'];
		fwrite($f, $output);
		fclose($f);
	}

	private function getpid(){
		$parts = array(
			APPLICATION_PATH,
			Config::get('app.path.misc'),
			'pid.txt'
		);
		$pidTxt = Helper::makePathFromParts($parts);
		if (file_exists($pidTxt)) {
			$f = fopen($pidTxt, "r");
			if (!$f){
				return $array();
			}
			$input = fread($f, filesize($pidTxt));
			$sliversTmp = explode("\n", $input);
			$slivers = array(
				'pid' => $sliversTmp[0],
				'cmd' => $sliversTmp[1],
				'output' => $sliversTmp[2]
			);

			fclose($f);
			return $slivers;
		}
		return array();
	}

	private function startCmdBg($cmd, $outputfile){
		$command = "/bin/bash -c '".$cmd." > ".$outputfile." 2>&1 & echo $!'";
		$pid = trim(shell_exec($command));
        for($i = 0; $i < 5; $i++){
            usleep(100000);
            $psoutput = trim(shell_exec($this->getPsStatement($pid, $cmd)));
            if (empty($psoutput)) {
                return array();
            }
        }
		return array('pid' => $pid, 'cmd' => $cmd, 'output' => $psoutput);
	}

	private function debugstartCmdBg($cmd, $outputfile){
		$result = shell_exec($cmd);
        return $cmd."\n".$outputfile."\n".$result."\n";
	}

	private function getPsStatement($pid, $cmd){
		// e.g: ps -aux | grep "[a]pache" | grep "1032"
		$firstchar = substr($cmd, 0, 1);
		$grepcmd = "[".preg_quote($firstchar)."]".preg_quote(substr($cmd, 1));
		return 'ps -aeo pid,uname,comm,args | grep "'.$grepcmd.'" | grep "'.$pid.'"';
	}

	private function isRunning($slivers){
		try {
			$result = trim(shell_exec($this->getPsStatement($slivers['pid'], $slivers['cmd'])));
			return empty($result) ? false : (strpos($slivers['output'], $result)  !== false);
		} catch (Exception $e){
		}
		return false;
	}

	private function dumpRunDotLog(){
			$parts = array(
				APPLICATION_PATH,
                Config::get('app.path.misc'),
                "runtime.log"
			);
			$pathOutputfile = Helper::makePathFromParts($parts);
            $path2 = Helper::makePathFromParts(
			array(
				APPLICATION_PATH,
                Config::get('app.path.misc'),
                "run.log"
			));
			$result = "";
			$result = $result.'<pre>';
			$result = $result.file_get_contents($pathOutputfile);
            $result = $result."\n\n\n";
            $result = $result.file_get_contents($path2);
			$result = $result.'</pre>';
			return $result;
	}
}
