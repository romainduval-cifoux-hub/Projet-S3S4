<?php 
class Facture {
    public $id;
    public $num;
    public $client;
    public $telephone;
    public $adresse;
    public $ville;
    public $codePostal;
    public $siret;
    public $date;
    public $type;
    public $status;
    public $reglement;
    public $datePaiement;
    public $nbRelance;
    public $lignes = [];

    public function __construct(array $row) {
        $this->id          = $row['idDoc'];
        $this->num         = $row['num'];
        $this->client      = $row['nomClient'];
        $this->telephone   = $row['telClient'];
        $this->adresse     = $row['addrClient'];
        $this->ville       = $row['villeClient'];
        $this->codePostal  = $row['codePostalClient'];
        $this->siret       = $row['siretClient'];
        $this->date        = $row['dateDoc'];
        $this->type        = $row['typeDoc'];
        $this->status      = $row['statusDoc'];
        $this->reglement   = $row['reglementDoc'];
        $this->datePaiement = $row['datePaiement'];
        $this->nbRelance   = $row['nbRelance'];
    }
}


?>