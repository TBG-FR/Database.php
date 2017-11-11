# Original Author - Credits
This 'Database.php' class has been made by Mr. Robin Frere (https://www.widee.fr/)  
It has been made for Computer Science students at Lyon1 University (Bourg-en-Bresse) as the correction of a practical work (TP3) in our PHP Course

# Usage (from the original provided README)

## Classe Database

Cette classe en php permettra une abstraction de la base de données

### installation

Ajouter le fichier Database.php dans votre projet puis l'inclure sur la page qui vous interresse :

```
<?php
require_once('folder/where/is/the/file/Database.php');
```

### initialisation 

En instanciant simplement la classe :

```
$db = new Database();
```

### Usages

> Méthode qui exécutera simplement une requète pas prévu dans la classe

```
$requete_personnalise = $db->query("SELECT * FROM Users");
```

> Méthode qui affichera les résultats d'une recherche dans la table Users :

```
$res = $db->read('Users', array(
    'conditions' => array(
        'nom LIKE' => 'H%', // les noms commençant par H
        'age >' => 20, // les personnes de plus de 20ans
        'code_postal' => "01000" // qui habitent bourg en bresse
    ),
    'fields' => array('id', 'nom', 'prenom'), // la liste des champs souhaités
    'order' => array('age DESC'), // par age décroissant
    'limit' => 10, // limité à 10 éléments
    'offset' => 5 // à partir du 6eme élément
));
```

> Méthode qui ajoute un utilisateur

```
$res = $db->save("Users", array(
    'nom' => 'Martin',
    'prenom' => 'Thomas',
    'age' => 34,
    'password' => 'monSuperMotDePasse' // le mot de passe sera crypté automatiquement selon la règle choisi dans la méthode hash()
));
```

> La même méthode pour mettre à jour un utilisateur existant en prenant l'id comme référence

```
$res = $db->save("Users", array(
    'id' => 245,
    'nom' => 'Martin',
    'prenom' => 'Thomas',
    'age' => 35,
    'password' => 'monAutreSuperMotDePasse' // le mot de passe sera crypté automatiquement selon la règle choisi dans la méthode hash()
));
```


> Supprimer l'utilisateur dont l'id est 123

```
$supprime = $db->delete('Users', 123);
```

> Mettre à jour des données de la table

```
$maj = $db->update(
    'Users', // la table à mettre à jour
    array(
        'status' => 'majeur' // les données à mettre à jour
    ),
    array(
        'conditions' => array('age >' => '18') // les conditions à remplir pour la mise à jour
    )
);
```