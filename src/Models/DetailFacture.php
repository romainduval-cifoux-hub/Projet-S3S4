<?php

class LigneFacture {
    public $designation;
    public $description;
    public $unite;
    public $quantite;
    public $prix;

    public function __construct(array $row) {
        $this->designation = $row['designation'];
        $this->description = $row['description'];
        $this->unite       = $row['unite'];
        $this->quantite    = $row['quantite'];
        $this->prix        = $row['prixUnitaire'];
    }
}

?>