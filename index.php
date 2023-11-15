<?php

class Game {
    private $Heros = [];
    private $enemies = [];
    private $currentHero;
    private $currentDifficulty;
    private $totalRounds = 0;

    // Constructeur de la classe Game
    public function __construct() {
        // Créer les héros
        $this->creationHero();

        // Créer les ennemis
        $this->creationEnnemi();

        // Choisir le héros
        $this->choixHero();

        // Choisir la difficulté
        $this->choixDifficulte();

        // Commencer le jeu
        $this->debut();
    }

    // Crée les héros à partir des données prédéfinies
    private function creationHero() {
        $listHeros = [
            ['nom' => 'Seong Gi-hun', 'bille' => 15, 'malus' => 2, 'bonus' => 1],
            ['nom' => 'Kang Sae-byeok', 'bille' => 25, 'malus' => 1, 'bonus' => 2],
            ['nom' => 'Cho Sang-woo', 'bille' => 35, 'malus' => 0, 'bonus' => 3],
        ];

        foreach ($listHeros as $heros) {
            $hero = new Hero($heros);
            $this->Heros[] = $hero;
        }
    }

    // Crée les ennemis avec des noms aléatoires et des caractéristiques aléatoires
    private function creationEnnemi() {
        $nomEnnemis = ['Kim', 'Lee', 'Park', 'Choi', 'Jung', 'Kang', 'Yoon', 'Im', 'Hong', 'Chung', 'Chang', 'Kwon', 'Shin', 'Oh', 'Han', 'Ryu', 'Park', 'Sim', 'Song', 'Yoo'];

        for ($i = 1; $i <= 20; $i++) {
            $caracteristiqueEnnemis = [
                'nom' => $nomEnnemis[array_rand($nomEnnemis)],
                'bille' => rand(1, 20),
                'age' => rand(18, 80),
            ];
            $enemy = new Enemy($caracteristiqueEnnemis);
            $this->enemies[] = $enemy;
        }
    }

    // Choisit aléatoirement un héros parmi ceux créés
    private function choixHero() {
        $this->currentHero = $this->Heros[rand(0, count($this->Heros) - 1)];
        echo "Vous avez choisi le héros: {$this->currentHero->getNom()}\n.<br>";
    }

    // Choisit aléatoirement une difficulté parmi les difficultés prédéfinies
    private function choixDifficulte() {
        $difficulties = ['Facile' => 5, 'Difficile' => 10, 'Impossible' => 20];
        $this->currentDifficulty = array_rand($difficulties);
        $this->totalRounds = $difficulties[$this->currentDifficulty];
        echo "Vous avez choisi la difficulté: {$this->currentDifficulty}\n.<br><br>";
    }

    // Démarre le jeu en exécutant une série de rounds et en affichant les résultats
    private function debut() {
        // Choisir le héros une fois avant le début du jeu
    
        for ($round = 1; $round <= $this->totalRounds; $round++) {
            $enemy = $this->enemies[$round - 1];
            echo "Round $round: Il reste {$this->currentHero->getBille()} billes.<br>";
    
            // Lancer le combat
            $result = $this->fight($enemy);
    
            echo $result . "<br>";
    
            // Vérifier si le héros a gagné ou a 0 billes, et terminer le jeu si nécessaire
            if ($this->currentHero->getBille() < 1) {
                echo "Dommage! Vous n'avez pas réussi à garder au moins une bille en votre possession. La partie est terminée.<br>";
                return;
            }
        }
    
        // Vérifier si le héros a gagné
        if ($this->currentHero->getBille() >= 1) {
            echo "Félicitations! Vous avez gagné et votre personnage a gagné 45,6 milliards de Won sud-coréen.<br>";
        }
    }
    
    // Fonction qui simule un combat entre le héros et un ennemi
    private function fight($enemy) {
        echo "Le joueur {$this->currentHero->getNom()} affronte {$enemy->getNom()} avec {$this->currentHero->getBille()} billes.<br>";

        $heroChoice = $this->currentHero->makeChoice();
        $nombreEnnemiBilles = $enemy->getBille();
        $isEnemyEven = $nombreEnnemiBilles % 2 === 0;

        // Vérifier si le joueur a plus de 70 ans pour le bonus de triche
        $isHeroCheating = $heroChoice['cheating'] && $enemy->getage() > 70;

        if ($isHeroCheating) {
            // Triche: le héros remporte automatiquement les billes sans deviner pair ou impair
            $this->currentHero->gagner($nombreEnnemiBilles);
            return "Victoire! Vous avez triché et remporté {$nombreEnnemiBilles} billes.<br>";
        } else {
            $result = $this->currentHero->applyResult(['even' => $heroChoice['even'], 'nombreEnnemiBilles' => $nombreEnnemiBilles]);

            if ($isEnemyEven === $heroChoice['even']) {
                // Le héros a deviné correctement
                return $result . "<br>Il vous reste {$this->currentHero->getBille()} billes.<br>";
            } else {
                // Le héros a deviné incorrectement
                return $result;
            }
        }
    }
}


class Character {
    protected $nom;
    protected $bille;

    // Constructeur de la classe Character
    public function __construct($data) {
        $this->nom = $data['nom'];
        $this->bille = $data['bille'];
    }

    // Getter pour obtenir le nom du personnage
    public function getNom() {
        return $this->nom;
    }

    // Getter pour obtenir le nombre de billes du personnage
    public function getBille() {
        return $this->bille;
    }

    // Méthode pour incrémenter le nombre de billes du personnage
    public function gagner($nombreBilles) {
        $this->bille += $nombreBilles;
    }

    // Méthode pour décrémenter le nombre de billes du personnage
    public function perdre($nombreBilles) {
        $this->bille -= $nombreBilles;
    }

}

// Classe Hero qui étend la classe Character
class Hero extends Character {
    private $bonus;
    private $malus;

    // Constructeur de la classe Hero
    public function __construct($data) {
        parent::__construct($data);
        $this->bonus = $data['bonus'];
        $this->malus = $data['malus'];
    }

    // Méthode pour prendre une décision (tricher ou non, pair ou impair)
    public function makeChoice() {
        $isHeroCheating = rand(0, 1);
        $isHeroEven = rand(0, 1);

        return ['cheating' => $isHeroCheating, 'even' => $isHeroEven];
    }

    // Méthode pour appliquer le résultat du combat
    public function applyResult($result) {
        $isEnemyEven = $this->isEven($result['nombreEnnemiBilles']);
    
        if ($result['even'] == $isEnemyEven) {
            // Le héros a deviné correctement
            $this->gagner($this->bonus);
            return "Victoire! Vous avez gagné {$this->bonus} billes.<br>";
        } else {
            // Le héros a deviné incorrectement
            $nombreBillePerdu = $result['nombreEnnemiBilles'] + $this->malus;
    
            // Vérifier si le héros a suffisamment de billes
            if ($this->bille >= $nombreBillePerdu) {
                $this->perdre($nombreBillePerdu);
                return "Défaite! Vous avez perdu {$nombreBillePerdu} billes. Il vous reste {$this->bille} billes.<br>";
            } else {
                $this->bille = 0;
                return "Défaite! Vous avez perdu {$nombreBillePerdu} billes. Il vous reste {$this->bille} billes.<br>";
            }
        }
    }
    
    // Méthode privée pour vérifier si un nombre est pair
    private function isEven($nombreEnnemiBilles) {
        return $nombreEnnemiBilles % 2 === 0;
    }
}

// Classe Enemy qui étend la classe Character
class Enemy extends Character {
    private $age;

    // Constructeur de la classe Enemy
    public function __construct($data) {
        parent::__construct($data);
        $this->age = $data['age'];
    }

    // Getter pour obtenir l'âge de l'ennemi
    public function getAge() {
        return $this->age;
    }
}

// Instanciation de la classe Game pour commencer le jeu
$game = new Game();

?>
