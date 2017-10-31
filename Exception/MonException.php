<?php
class MonException extends Exception
{
  public function __construct($message, $code = 0)
  {
    parent::__construct($message, $code);
  }
  
  public function __toString()
  {
    return $this->message;
  }
}

function additionner($a, $b)
{
  if (!is_numeric($a) || !is_numeric($b))
  {
    throw new MonException('Les deux paramètres doivent être des nombres'); // On lance une exception "MonException".
  }
  
  if (func_num_args() > 2)
  {
    throw new Exception('Pas plus de deux arguments ne doivent être passés à la fonction'); // Cette fois-ci, on lance une exception "Exception".
  }
  
  return $a + $b;
}

try // Nous allons essayer d'effectuer les instructions situées dans ce bloc.
{
  echo additionner(12, 3), '<br />';
  echo additionner(15, 54, 45), '<br />';
}

catch (MonException $e) // Nous allons attraper les exceptions "MonException" s'il y en a une qui est levée.
{
  echo '[MonException] : ', $e; // On affiche le message d'erreur grâce à la méthode __toString que l'on a écrite.
}

catch (Exception $e) // Si l'exception n'est toujours pas attrapée, alors nous allons essayer d'attraper l'exception "Exception".
{
  echo '[Exception] : ', $e->getMessage(); // La méthode __toString() nous affiche trop d'informations, nous voulons juste le message d'erreur.
}

echo '<br />Fin du script'; // Ce message s'affiche, cela prouve bien que le script est exécuté jusqu'au bout.
