<?php require '../config.php'; require 'UtilisateurC.php'; $uc = new UtilisateurC(); try { $liste = $uc->ListeUtilisateurs(); print_r($liste); } catch (Exception $e) { echo $e->getMessage(); }
