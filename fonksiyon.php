<?php

$vt = new PDO('sqlite:veritabani.db');
session_start();
if(isset($_GET['sil'])){
    if(isset($_SESSION['yetki'])&&$_SESSION['yetki']==1){
        global $vt;
        $faturaID=$_GET['sil'];
        $sonuc = $vt->query("DELETE FROM fatura WHERE id=$faturaID");
        header("Location:admin.php");
    }
    else{
        header("Location:index.php");
    }
}
if(isset($_GET['kullanicisil'])){
    if(isset($_SESSION['yetki'])&&$_SESSION['yetki']==1){
        global $vt;
        $kid=$_GET['kullanicisil'];
        $sonuc = $vt->query("DELETE FROM kullanici WHERE id=$kid");
        $sonuc = $vt->query("DELETE FROM fatura WHERE kullaniciid=$kid");
        header("Location:admin.php");
    }
    else{
        header("Location:index.php");
    }
}
if(isset($_POST['faturaekle'])){
    $kid = $_SESSION['kid'];
    $kurumadi = $_POST['kurumadi'];
    $tutar = $_POST['tutar'];
    $tarih = $_POST['tarih'];
    $sonuc = $vt->query("INSERT INTO Fatura(kullaniciid,kurumadi,tutar,tarih,odendimi) VALUES($kid,'$kurumadi',$tutar,'$tarih',0)");
    if($_SESSION['yetki']==1){
        header("Location:admin.php");
    }
    else{
        header("Location:index.php");
    }
}
if(isset($_GET['cikis'])){
    session_destroy();
    session_start();
    header("Location:index.php");
}
if(isset($_POST['girisyap'])){
    $giris=girisYap($_POST['kullaniciadi'],$_POST['sifre']);
    if($giris!==false){
        $_SESSION['kid']=$giris['id'];
        $_SESSION['yetki']=$giris['yetki'];
        header("Location:index.php");
    }
    else{
        header("Location:index.php?girisbasarisiz");
    }
}
if(isset($_POST['kayitol'])){
    $kullaniciadi = $_POST['kullaniciadi'];
    $sifre= $_POST['sifre'];
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $eposta = $_POST['eposta'];
    $adres = $_POST['adres'];
    if(empty($kullaniciadi)||empty($sifre)||empty($eposta)){
        header("location:index.php?KayitBasarisiz"); 
    }
    else{
        if(kayitOl($kullaniciadi,$sifre,$eposta,$ad ,$soyad,$adres)){
            $giris = girisYap($kullaniciadi,$sifre);
            $_SESSION['kid']=$giris['id'];
            $_SESSION['yetki']=$giris['yetki'];
            header("location:index.php?KayitBasarili"); 
        }
        else{
            header("location:index.php?KayitBasarisiz"); 
        }
    }
}
if(isset($_GET['ode'])){
    $faturaID = $_GET['ode'];
    $vt->query("UPDATE Fatura SET odendimi=1 WHERE id=$faturaID");
    header("Location:fatura-icerigi.php?kid=$faturaID");
}
function kayitOl($kadi, $sifre, $eposta,$ad, $soyad,$adres)
{
    global $vt;
    $sonuc = $vt->query("INSERT INTO Kullanici(kullaniciadi,sifre,eposta,ad,soyad,adres,yetki) VALUES('$kadi','$sifre','$eposta','$ad','$soyad','$adres',0)");
    if ($sonuc == false) return false;
    else return true;
}
function girisYap($kadi, $sifre)
{
    global $vt;
    $sorgu = "SELECT * FROM kullanici WHERE kullaniciadi='$kadi' AND sifre='$sifre'";
    $sonuclar = $vt->query($sorgu);
    if ($satir = $sonuclar->fetch()) {
        return $satir;
    } else return false;
}
function kullaniciBilgisi($kid)
{
    global $vt;
    $sorgu = "SELECT * FROM kullanici WHERE id=$kid";
    $sonuclar = $vt->query($sorgu);
    if ($satir = $sonuclar->fetch()) {
        return $satir;
    } 
}
function tumKullanicilar()
{
    global $vt;
    $sorgu = "SELECT * FROM Kullanici WHERE kullaniciadi NOT LIKE 'admin'";
    $sonuclar = $vt->query($sorgu);
    $dizi = [];
    while ($satir = $sonuclar->fetch()) {
        array_push($dizi,$satir);
    } 
    return $dizi;
}

function tumFaturalar()
{
    global $vt;
    $kid = $_SESSION['kid'];
    $sorgu = "SELECT * FROM Fatura where kullaniciid=$kid";
    $sonuclar = $vt->query($sorgu);
    $dizi = [];
    while ($satir = $sonuclar->fetch()) {
        array_push($dizi,$satir);
    } 
    return $dizi;
}
function tumFaturalarAdmin()
{
    global $vt;
    $sorgu = "SELECT Fatura.*,Kullanici.* FROM Fatura INNER JOIN Kullanici ON Fatura.kullaniciid=Kullanici.id";
    $sonuclar = $vt->query($sorgu);
    $dizi = [];
    while ($satir = $sonuclar->fetch()) {
        array_push($dizi,$satir);
    } 
    return $dizi;
}
function faturaBilgisi($faturaID)
{
    global $vt;
    $sorgu = "SELECT * FROM fatura WHERE id=$faturaID";
    $sonuclar = $vt->query($sorgu);
    if ($satir = $sonuclar->fetch()) {
        return $satir;
    } 
}
function girisYapildimi()
{
    if(isset($_SESSION['kid']))return true;
    return false;
}