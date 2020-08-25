<?php
#VERSION 1.4
#02.12.2019 09:16
// S-1-5-21-2507375265-954448677-3925829934-12239
// S-1-5-21-2507375265-954448677-3925829934-1425
// S-1-5-21-2507375265-954448677-3925829934-500
// S-1-5-21-2507375265-954448677-3925829934-12240
define("USERNAME", "administrator");
define("PASSWORD", "Passw0rd");
define("SERVER_IP", "10.0.2.101");
define("SERVER_LDAP_PORT", 636);
define("DOMAIN_DN", "domain.lab");
define("DOMAIN_DN_DC", "dc=domain,dc=lab");

$time = date('Y-m-d H-i-s');
$samba = new LdapConnection(SERVER_IP, USERNAME, PASSWORD, true, DOMAIN_DN, SERVER_LDAP_PORT);

if(!is_dir("log")){
    mkdir("log");
}
if(!is_dir("log" ."/kullanici")){
    mkdir("log" . "/kullanici");   
}
if(!is_dir("log" ."/grup")){
    mkdir("log" . "/grup");   
}
if(!is_dir("log" ."/kendi_grubuna_eklenme")){
    mkdir("log" . "/kendi_grubuna_eklenme");   
}
if(!is_dir("log" ."/security_gruba_eklenme")){
    mkdir("log" . "/security_gruba_eklenme");   
}
if(!is_dir("log" ."/dist_gruba_eklenme")){
    mkdir("log" . "/dist_gruba_eklenme");   
}
$eklenen_kullanici = fopen("log" . "/kullanici" ."/basarili","w");
$eklenemeyen_kullanici = fopen("log" . "/kullanici" ."/basarisiz","w");

$eklenen_grup = fopen("log" . "/grup" ."/basarili","w");
$eklenemeyen_grup = fopen("log" . "/grup" ."/basarisiz","w");

$kendi_grubuna_eklenen= fopen("log" . "/kendi_grubuna_eklenme" ."/basarili","w");
$kendi_grubuna_eklenemeyen = fopen("log" . "/kendi_grubuna_eklenme" ."/basarisiz","w");

$security_gruba_eklenen = fopen("log" . "/security_gruba_eklenme" ."/basarili","w");
$security_gruba_eklenemeyen = fopen("log" . "/security_gruba_eklenme" ."/basarisiz","w");

$dist_gruba_eklenen = fopen("log" . "/dist_gruba_eklenme" ."/basarili","w");
$dist_gruba_eklenemeyen= fopen("log" . "/dist_gruba_eklenme" ."/basarisiz","w");
$run_log = fopen("log" . "/run_log.txt","w");

$objects = [];
$counts = [];
$sayilar = array(0,0,0,0,0,0,0,0,0,0);
echo("Security Grup Ekleniyor...\n");
fwrite($run_log,"Security Grup Ekleniyor..."."\n" ); 
    $cleaner = [];
    $cleaner["cn"] ="11223344";
    $cleaner["sAMAccountName"] = "11223344";
    $cleaner["objectClass"] = ["top","group"];
    $cleaner["groupType"] = "-2147483646";
    $dn = "cn=11223344,"  . DOMAIN_DN_DC ;
    $flag = @$samba->addObject($samba->escape($dn), $cleaner);
    if ($flag === true) {
    echo("Security Grup Başarıyla Eklendi...\n");
    fwrite($run_log,"Security Grup Başarıyla Eklendi..."."\n" ); 
    } else {
    echo("Security Grup EKLENEMEDİ!!!!!...".$flag."\n");
    fwrite($run_log,"Security Grup EKLENEMEDİ!!!!!...".$flag."\n" ); 
    }
    echo("Dist Grup Ekleniyor...\n");
    fwrite($run_log,"Dist Grup Ekleniyor..."."\n" ); 

    $cn="11223355";
    $cleaner = [];
    $cleaner["cn"] ="11223355";
    $cleaner["sAMAccountName"] = "11223355";
    $cleaner["objectClass"] = ["top","group"];
    $cleaner["groupType"] = "2";
    $dn = "cn=11223355,"  . DOMAIN_DN_DC ;
    $flag = @$samba->addObject($samba->escape($dn), $cleaner);
    if ($flag === true) {
    echo("Dist Grup Başarıyla Eklendi...\n");
    fwrite($run_log,"Dist Grup Başarıyla Eklendi...".$flag."\n" ); 
    } else {
    echo("Dist Grup EKLENEMEDİ!!!!!...".$flag."\n");
    fwrite($run_log,"Dist Grup EKLENEMEDİ!!!!!...".$flag."\n" ); 
    }



/* USERS ADD BEGIN */
echo("Kullanicilar Ekleniyor...\n");
echo("Kullanici Gruplari Ekleniyor...\n");

fwrite($run_log,"Kullanicilar Ekleniyor..."."\n" ); 
fwrite($run_log,"Kullanıcı Grupları Ekleniyor..."."\n" ); 

$counter = 0;
for ($x = 10000000200; $x <= 10000000300; $x++) {
//foreach ([] as $current) {
    $cleaner = [];
    $cleaner["useraccountcontrol"] = "512";
    $cleaner["cn"] = $x;
    $cleaner["sAMAccountName"] = $x;
    $dn = "cn=" . $x . ",cn=Users," .  DOMAIN_DN_DC;
    // if($samba->check($dn) == true){
    //     continue;
    // }
    $cleaner["objectClass"] = ["top","person","user","organizationalPerson","posixAccount","shadowAccount"];
    $cleaner["unicodepwd"] = mb_convert_encoding("\"" ."Passw0rd" . "\"", "UTF-16LE");
    
    //Generate Mail
    $name =  "user" . $counter;
    $surname =  "surname" . $counter;
    $newMail = "user".$counter .".surname". "@" . $samba->getDomain();
    $cleaner["displayName"] = "User". $counter ." Surname". $counter;
    $cleaner["mail"] = $newMail;
    $cleaner["uid"] = $x;
    $cleaner["uidNumber"] = 2000 + $counter;
    $cleaner["gidNumber"] = 2000 + $counter;
    $cleaner["homeDirectory"] = "/home/".$x;
    $cleaner["loginShell"] = "/bin/bash";

    $flag = @$samba->addObject($samba->escape($dn), $cleaner);
    if ($flag === true) {
        fwrite($eklenen_kullanici, "BAŞARILI----->" . $dn ."\n" ); 
        $sayilar[0]+=1;
   } else {
        fwrite($eklenemeyen_kullanici,"BAŞARISIZ----->".$dn. $flag ."\n" ); 
        $sayilar[1]+=1;
   }

    $cleaner2 = [];
    $cleaner2["name"] = $x;
    $cleaner2["cn"] = $x;
    $cleaner2["sAMAccountName"] = "g-" . $x;
    $cleaner2["objectClass"] = ["top","group"];
    $dn2 = "cn=" . $x . ",ou=Groups," . DOMAIN_DN_DC ;

   $flag2 = @$samba->addObject($samba->escape($dn2), $cleaner2);
   echo $dn2;
   print_r($cleaner2);
  
   if ($flag2 === true) {
        fwrite($eklenen_grup, "BAŞARILI----->" . $dn2 ."\n" ); 
        $sayilar[2]+=1;
   } else {
        fwrite($eklenemeyen_grup,"BAŞARISIZ----->".$dn2. $flag2 ."\n" ); 
        $sayilar[3]+=1;
}
}
$counter += 1;
echo("Kullanicilar Eklendi.\n");
echo("Kullanici Gruplari Eklendi.\n");

fwrite($run_log,"Kullanıcılar Eklendi..."."\n" ); 
fwrite($run_log,"Kullanıcı Grupları Eklendi..."."\n" ); 

/* USERS ADD END */
for ($x = 10000000200; $x <= 10000000300; $x++) {
    $cleaner = [];
    $dn = "CN=".$x .",OU=Groups,". DOMAIN_DN_DC;
    $member = "cn=" . $x . ",cn=Users," .  DOMAIN_DN_DC;
    $flag = @$samba->addAttribute($dn, ["member" => [$member]]);

    if ($flag === true) {
        fwrite($kendi_grubuna_eklenen, "BAŞARILI----->" . $dn ."\n" ); 
        $sayilar[4]+=1;
    } else {
        fwrite($kendi_grubuna_eklenemeyen,"BAŞARISIZ----->".$dn. $flag ."\n" );
        $sayilar[5]+=1; 
    }

    $cleaner = [];
    $dn = "CN=11223344,". DOMAIN_DN_DC;
    $member = "cn=" . $x . ",cn=Users," .  DOMAIN_DN_DC;
    $flag = @$samba->addAttribute($dn, ["member" => [$member]]);
    if ($flag === true) {
        fwrite($security_gruba_eklenen, "BAŞARILI----->" . $dn ."\n" ); 
        $sayilar[6]+=1;
    } else {
        fwrite($security_gruba_eklenemeyen,"BAŞARISIZ----->".$dn. $flag ."\n" ); 
        $sayilar[7]+=1;
    }
    $cleaner = [];
    $dn = "CN=11223355," . DOMAIN_DN_DC;
    $menber = "cn=" . $x . ",cn=Users," .  DOMAIN_DN_DC;
    $flag = @$samba->addAttribute($dn, ["member" => [$member]]);

    if ($flag === true) {
        fwrite($dist_gruba_eklenen, "BAŞARILI----->" . $dn ."\n" ); 
        $sayilar[8]+=1;
    } else {
        fwrite($dist_gruba_eklenemeyen,"BAŞARISIZ----->".$dn. $flag ."\n" ); 
        $sayilar[9]+=1;
    }
}
echo("GENEL BİLGİLER\n");
echo("################################\n");
echo("Eklenen kullanici sayisi:".$sayilar[0]."\n");
echo("EKLENEMEYEN kullanici sayisi:".$sayilar[1]."\n");
echo("Eklenen kullanici #Grup# sayisi:".$sayilar[2]."\n");
echo("EKLENEMEYEN kullanici #Grup# sayisi:".$sayilar[3]."\n");
echo("Kendi Grubuna Eklenen kullanici sayisi:".$sayilar[4]."\n");
echo("Kendi Grubuna EKLENEMEYEN kullanici sayisi:".$sayilar[5]."\n");
echo("Security Gruba Eklenen kullanici sayisi:".$sayilar[6]."\n");
echo("Security Gruba EKLENEMEYEN kullanici sayisi:".$sayilar[7]."\n");
echo("Dist Gruba Eklenen kullanici sayisi:".$sayilar[8]."\n");
echo("Dist Gruba EKLENEMEYEN kullanici sayisi:".$sayilar[9]."\n");

function randomPassword()
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 12; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

/*KOD BITIS*/

function startsWith($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

class LdapConnection
{
    private $connection;
    private $dn = "";
    private $domain = "";
    private $fqdn = "";
    private $ip = null;
    private $username = null;
    private $password = null;
    private $ssl = false;
    private $port = 389;

    public function __construct($ip, $username, $password, $ssl = false, $domain, $port)
    {
        $this->ip = $ip;
        $this->username = $username;
        $this->password = $password;
        $this->domain = $domain;
        $this->ssl = $ssl;
        $this->port = $port;

        if (substr($this->username, 0, 2) == "cn" || substr($this->username, 0, 2) == "CN") {
            $this->dn = $this->username;
        } else {
            $this->dn = $this->username . "@" . $this->getDomain();
        }

        $this->connection = $this->initWindows();
    }

    public function read($dn)
    {
        $object = ldap_read($this->connection, $this->escape($dn), "(objectclass=*)");
        //dd($object,$dn,$this->escape($dn));
        $entries = ldap_get_entries($this->connection, $object)[0];

        foreach ($entries as $key => $value) {
            if (is_int($key)) {
                unset($entries[$key]);
            }
        }
        unset($entries["count"]);
        return $entries;
    }

    private function initWindows()
    {
        // Create Ldap Connection Object
        if ($this->ssl) {
            $ldap_connection = ldap_connect('ldaps://' . $this->ip, $this->port);
        } else {
            $ldap_connection = ldap_connect('ldap://' . $this->ip, $this->port);
        }

        // Set Protocol Version
        ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);

        ldap_set_option($ldap_connection, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);

        ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0);
        // Try to Bind Ldap
        try {
            ldap_bind($ldap_connection, $this->dn, $this->password);
        } catch (Exception $e) {
            dd($e->getMessage());
        }

        // Return Object to use it later.
        return $ldap_connection;
    }

    public function searchAsTree($filter, $attributeList = ["dn"], $page = "1", $perPage = "10")
    {
        $results = $this->search($filter, $attributeList, $page, $perPage);

        // Create Array To use it later on.
        $array = [];
        for ($i = 0; $i < $results["count"]; $i++) {
            $user = $results[$i]["dn"];
            $arr = explode(",", $user);
            $arr = array_reverse($arr);
            $res = array();
            $t = &$res;
            foreach ($arr as $k) {
                if (empty($t[$k])) {
                    if (!starts_with($k, "cn")) {
                        $t[$k] = array();
                    } else {
                        $t[$k] = $k;
                    }
                    $t = &$t[$k];
                }
            }
            unset($t);
            $array = array_merge_recursive($array, $res);
        }
        return $array;
    }

    public function check($dn){
		return @ldap_read($this->connection,$this->escape($dn),"(objectclass=*)");
    }
    
    public function escape($query)
    {
        $query = rawurldecode($query);
        $query = html_entity_decode($query);
        $query = ldap_escape(rawurldecode($query), "", LDAP_ESCAPE_FILTER);
        return $query;
    }

    public function search($filter, $options = [])
    {
        $searchOn = (array_key_exists("searchOn", $options) && $options["searchOn"] != null) ? $options["searchOn"] : $this->domain;
        $page = (array_key_exists("page", $options)) ? $options["page"] : "1";
        $perPage = (array_key_exists("perPage", $options)) ? $options["perPage"] : "500";
        $attributeList = (array_key_exists("attributeList", $options)) ? $options["attributeList"] : ["dn"];
        $stopOn = (array_key_exists("stopOn", $options)) ? $options["stopOn"] : "-1";

        $filter = html_entity_decode($filter);
        $searchOn = html_entity_decode($searchOn);

        // Set Variables
        $cookie = "";
        $size = 0;
        $entries = [];
        $loop = 0;

        // First, retrieve real size of search.
        do {

            // Break If that's enough
            if ($stopOn != "-1" && $size > $stopOn) {
                break;
            }

            // First Increase Loop Count
            $loop++;

            // Limit Search for each loop.
            ldap_control_paged_result($this->connection, intval($perPage), true, $cookie);

            // Make Search
            $search = ldap_search($this->connection, $searchOn, $filter, $attributeList);

            // Retrieve Entries if specified
            if ($loop == intval($page) || $page == "-1") {
                $entries = array_merge(ldap_get_entries($this->connection, $search), $entries);
            }

            // Count Results and sum with total size.
            $size += ldap_count_entries($this->connection, $search);

            // Update Cookie
            ldap_control_paged_result_response($this->connection, $search, $cookie);
        } while ($cookie !== null && $cookie != '');

        // Return what we have.
        return [$size, $entries];
    }

    public function addObject($cn, $data)
    {
        $flag = ldap_add($this->connection, $cn, $data);
        return $flag ? true : ldap_error($this->connection);
    }

    public function getAttributes($cn)
    {
        $cn = html_entity_decode($cn);
        $cn = ldap_escape($cn);
        $search = ldap_search($this->connection, $this->domain, '(distinguishedname=' . $cn . ')');
        $first = ldap_first_entry($this->connection, $search);
        return ldap_get_attributes($this->connection, $first);
    }

    public function convertTime($ldapTime)
    {
        $secsAfterADEpoch = $ldapTime / 10000000;
        $ADToUnixConverter = ((1970 - 1601) * 365 - 3 + round((1970 - 1601) / 4)) * 86400;
        return intval($secsAfterADEpoch - $ADToUnixConverter);
    }

    public function countSearch($query)
    {
        $newDomain = "DC=" . implode(",DC=",explode(".", DOMAIN_DN));
        $search = ldap_search($this->connection, $newDomain, $query);

        return ldap_count_entries($this->connection, $search);
    }

    public function updateAttributes($cn, $array)
    {
        $toUpdate = [];
        $toDelete = [];
        foreach ($array as $key => $item) {
            if ($item == null) {
                $toDelete[$key] = array();
                continue;
            }
            $toUpdate[$key] = $item;
        }
        $flagUpdate = true;
        $flagDelete = true;
        if (count($toUpdate)) {
            $flagUpdate = ldap_mod_replace($this->connection, $cn, $toUpdate);
        }

        if (count($toDelete)) {
            $flagDelete = ldap_modify($this->connection, $cn, $toDelete);
        }

        return $flagUpdate && $flagDelete;
    }

    public function removeObject($cn)
    {
        return ldap_delete($this->connection, $cn);
    }

    public function addAttribute($cn, $array)
    {
        $cn = html_entity_decode($cn);
        try {
            return ldap_mod_add($this->connection, $cn, $array);
        } catch (Exception $exception) {
            return false;
        }
    }

    public function deleteAttribute($ou, $array)
    {
        $ou = html_entity_decode($ou);
        return ldap_mod_del($this->connection, $ou, $array);
    }

    public function getDomain()
    {
        $domain = $this->domain;
        $domain = str_replace("dc=", "", strtolower($domain));
        return str_replace(",", ".", $domain);
    }

    public function getDC()
    {
        return $this->domain;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function changePassword($ou, $password)
    {
        return $this->updateAttributes($ou, ["unicodepwd" => mb_convert_encoding("\"" . $password . "\"", "UTF-16LE")]);
    }

    public function renameOU($dn, $ou, $cn)
    {
        $flag = ldap_rename($this->connection, $dn, $cn, $ou, true);
        return $flag ? true : ldap_error($this->connection);
    }

    public function getFQDN()
    {
        return $this->fqdn;
    }

    public function list($searchDN, $filter = "distinguishedName=*", $attributes = ["dn", "objectClass"])
    {
        $objects = ldap_list($this->connection, $searchDN, $filter, $attributes);
        return ldap_get_entries($this->connection, $objects);
    }
}
