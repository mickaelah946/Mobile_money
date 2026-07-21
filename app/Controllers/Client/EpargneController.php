<php
   namespace App\Controllers\Client;

   use  App\Controllers\BaseController;
   use App\Models\CompteModel;

   class EpargneController extends BaseController
    {
      protected CompteModel $compteModel;

        public function_construct()
         {
        $this->compteModel = new CompteModel();
        } 

      public function edit()
       {
        $compte = $this->compteModel->findByClientId((int) $this->session->get('clientId'));

        if(! $compte) {
            return redirect()->to('/client/dashboard')->with('error','Votre compte est introuvable.');
        }

        return view('client/epargne/edit',['compte'=> $compte]);
     }
  
      public function update()
    {
         $compte = $this->compteModel->findByClientId((int) $this->session->get('clientId'));
    }
    
    if(! $compte) {
        return redirect()->to('?client/dashboard')
    }
}
