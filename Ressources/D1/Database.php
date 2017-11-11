<?php 

/**
 * ===== ===== ===== ===== ===== ===== ===== ===== =====
 * AUTHOR : Mr. Robin Frere (https://www.widee.fr/)
 * CHECK THE README FOR ADDITIONNAL INFORMATION
 * ===== ===== ===== ===== ===== ===== ===== ===== =====
 */

/**
 * Class Database
 * pour une abstraction totale de la base de données, le but étant de simplifier le code
 */
class Database
{
    /**
     * @var null stocke l'instance de PDO
     */
    private $pdo = null;

    /**
     * @var string stocke le serveur mysql
     */
    private $dbhost;

    /**
     * @var string le nom de la base de données mysql
     */
    private $dbname;

    /**
     * @var string le nom de l'utilisateur mysql
     */
    private $dbuser;

    /**
     * @var string le mot de passe d'accès à mysql
     */
    private $dbpass;

    /**
     * pour filtrer les données en encodant les caractères interprétables
     * @param array $datas
     * @return array
     */
    private function filtre($datas){
        foreach ($datas as $k => $data){
            $datas[$k] = htmlspecialchars($data);
        }
        return $datas;
    }

    /**
     * @param string $password
     * @return string hashed
     */
    private function hash($password){
        // on choisira ici la méthode de cryptage de mot de passe
        //return md5($password);
        $options = array(
            'salt' => 'Zbk6s2i!!?vs+_tM2-&-=mvTpW4ReC945VH64Vb9&7$+R2UxW6Gb!@6eH#7P' // on choisi un code pour que l'algo de cryptage soit réversible
        );
        return password_hash($password, CRYPT_BLOWFISH, $options);

    }

    /**
     * une fonction intermédiaire qui formate les conditions d'un select
     * @param array $data
     * @return array
     */
    private function conditions($data){
        $condition = "";
        $values = array();
        if(count($data) > 0){
            foreach ($data as $key => $value){
                if($key == "password"){
                    $value = $this->hash($value); // si le mot de passe est recherché il faut chercher le mot de passe crypté
                }
                if($condition != ""){
                    $condition .= " AND ";
                }
                $operateur = explode(" ", $key); // gestion des opérateurs dans les conditions : <= >= != <> LIKE
                if(isset($operateur[1])){
                    $condition .= $operateur[0] . " " . $operateur[1] . " ? ";
                }else{
                    $condition .= $operateur[0] . " = ? ";
                }
                $values[] = $value;
            }
        }
        return array('condition' => $condition, 'values' => $values);
    }

    /**
     * Si les champs created et/ou updated sont présents dans la table, on les remplira automatiquement
     * @param string $table la table à tester
     * @param bool $create
     * @return array
     */
    private function setDate($table, $create = false){
        $data = array();
        if($create){
            $list = $this->query("SHOW COLUMNS FROM $table WHERE field = 'created'");
            if(count($list) > 0){
                $data['created'] =  date("Y-m-d H:i:s");
            }
        }
        $list = $this->query("SHOW COLUMNS FROM $table WHERE field = 'updated'");
        if(count($list) > 0){
            $data['updated'] =  date("Y-m-d H:i:s");
        }
        return $data;
    }

    /**
     * Database constructor. assigne les variable de connexion à la base de données
     * @param string $name
     * @param string $host
     * @param string $user
     * @param string $password
     */
    public function __construct($name = 'iut', $host = 'DB', $user = 'root', $password = 'root'){
        $this->dbhost = $host;
        $this->dbname = $name;
        $this->dbuser = $user;
        $this->dbpass = $password;
    }

    /**
     * initialise PDO si l'instance n'est pas créé, retourne l'instance si elle est déjà créé
     * @return PDO
     */
    private function getPdo()
    {
        if($this->pdo === null){
            try{
                $this->pdo = new PDO("mysql:host={$this->dbhost};port=3306;dbname={$this->dbname}", $this->dbuser, $this->dbpass);
            }catch (PDOException $e){
                die("Erreur : " . $e->getMessage());
            }
        }
        return $this->pdo;
    }

    /**
     * execute une requète sql en mode préparé
     * @param string $query la requète à exécuter
     * @param array $values les valeurs à injecter dans la requète préparé
     * @param bool $one si on souhaite un seul résultat
     * @return array|boolean|object en fonction du type de résultat généré par la requète
     */
    public function query($query, $values = array(), $one = false){
        $req = $this->getPdo()->prepare($query);
        $req->execute($values);
        $req->setFetchMode(PDO::FETCH_OBJ);
        if(is_bool($req)){
            return $req;
        }
        if($one){
            return $req->fetch();
        }else{
            return $req->fetchAll();
        }
    }

    /**
     * lire des données d'une $table en indiquant une série d'options au format d'un tableau (voir structure tableau $default)
     * @param string $table
     * @param array $options
     * @param bool $one
     * @return array|bool|object les résultats de la requète de recherche
     */
    public function read($table, $options = array(), $one = false){
        // on détermine un tableau de valeur par défaut pour éviter les test d'existance d'index
        $default = array(
            "conditions" => array(), // exemple : array("champ" => "valeur") correspond à WHERE champ='valeur'
            "fields" => array(), // exemple : array('col1', 'col2', 'col6') correspond à SELECT col1,col2,col6 FROM ...
            "limit" => null, // exemple : 10 correspond à LIMIT 10
            "offset" => 0, // ne fonctionne que si limit est défini, en donnant une valeur numérique, correspondra à l'offset de départ
            "order" => array() // exemple : array('col5', 'col7 DESC') correspond à ORDER BY col5, col7 DESC
        );
        $options = array_replace($default, $options); // on fusionne les options avec le tableau par défaut
        $condition = $this->conditions($options['conditions']);

        // on crée la chaine des champs à selectionner
        if(count($options['fields']) > 0){
            $fields = implode(',', $options['fields']);
        }else{
            $fields = "*";
        }

        $req = "SELECT " . $fields . " FROM " . $table;
        if($condition['condition'] != ""){
            $req .= " WHERE " . $condition['condition'];
        }

        // on crée la chaine pour le ORDER BY
        if(count($options['order'])> 0){
            $req .= " ORDER BY " . implode(', ', $options['order']);
        }

        // on ajoute la limite de la requète
        if($options['limit']){
            $req .= " LIMIT " . $options['offset'] . ", " . $options['limit'];
        }

        // on retourne le tableau des résultats
        return $this->query($req, $condition['values'], $one);
    }

    /**
     * ajoute des données dans la table (si l'id est défini dans $datas alors la requète fera un UPDATE
     * @param string $table le nom de la table
     * @param array $datas un tableau des valeurs à ajouter ex: array('col1' => 'val1', 'col2' => 'val2', 'col3' => 'val3', ...)
     * @return bool
     */
    public function save($table, $datas){
        if(count($datas) == 0){
            return false;
        }
        $datas = $this->filtre($datas);
        if(isset($datas['id']) && is_numeric($datas['id'])){
            $id = $datas['id'];
            unset($datas['id']);
        }
        if(isset($datas['password'])){ // on crypte le mot de passe si il fait parti des données
            $datas['password'] = $this->hash($datas['password']);
        }
        $datas = array_merge($datas, $this->setDate($table, !isset($id))); // on appelle la méthode qui vérifie pour compléter les dates created/updated
        $keys = array_keys($datas);
        $values = substr(str_repeat('?,',count($keys)),0,-1);
        if(isset($id)){ // si l'id existe on va faire une mise à jour
            $fields = implode('=?, ',$keys);
            $req = "UPDATE " . $table . " SET $fields=? WHERE id=" . $id;
        }else{ // sinon on fait une insertion
            $fields = implode(', ',$keys);
            $req = "INSERT INTO " . $table . " ($fields) VALUES ($values)";
        }
        return $this->query($req, array_values($datas));
    }

    /**
     * supprime un élément de la table dont l'id est $id
     * @param string $table le nom de la table
     * @param int $id l'id de l'élément à supprimer
     * @return bool
     */
    public function delete($table, $id){
        if(is_numeric($id)) {
            $req = "DELETE FROM " . $table . " WHERE id=" . $id;
            return $this->getPdo()->exec($req) == 1;
        }
        return false;
    }

    /**
     * Méthode pour faire un UPDATE sur une table
     * @todo cette méthode n'est pas optimale, il faudra revoir sa conception pour éviter de réaliser une boucle
     * @param string $table
     * @param array $datas
     * @param array $conditions
     * @return bool|int le nombre de champs concernés
     */
    public function update($table, $datas, $conditions){
        $search = $this->read($table, $conditions); // on vérifie si il y a des correspondances dans la table selon les conditions demandées
        if(!$search){
            return false; // retourne false si aucun résultat
        }
        $cpt = 0;
        foreach ($search as $key => $value){
            $cpt++;
            $datas = array_merge($datas, $this->setDate($table)); // on appelle la méthode qui vérifie pour compléter les dates created/updated
            $datas["id"] = $value->id;
            $this->save($table, $datas); // met à jour chaque élément trouvé dans le $search
        }
        return $cpt;
    }
}



// on évitera de fermer la balise php pour ne pas injecter de caratères invible sur les pages parentes
