<?php
class Exception
{
  protected $message = 'exception inconnu'; // Message de l'exception.
  protected $code = 0; // Code de l'exception défini par l'utilisateur.
  protected $file; // Nom du fichier source de l'exception.
  protected $line; // Ligne de la source de l'exception.
  
  final function getMessage(); // Message de l'exception.
  final function getCode(); // Code de l'exception.
  final function getFile(); // Nom du fichier source.
  final function getLine(); // Ligne du fichier source.
  final function getTrace(); // Un tableau de backtrace().
  final function getTraceAsString(); // Chaîne formattée de trace.
  
  /* Remplacable */
  function __construct ($message = NULL, $code = 0);
  function __toString(); // Chaîne formatée pour l'affichage.
}
