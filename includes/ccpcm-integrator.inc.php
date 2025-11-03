<?php

class ccpcm_integrator extends ccpcm_object {
  # FIFAM
  public function Projections_Per_Days($inputs = [], $dpi = 150) {
    return $this->ccpcm->data->jsondb->get('planning','planning');
  }
  public function Projections_Per_Cinema($inputs = [], $dpi = 150) {
    return $this->ccpcm->data->jsondb->get('planning','planning');
  }
  public function Projections_Per_Room($inputs = [], $dpi = 150) {
    return $this->ccpcm->data->jsondb->get('planning','planning');
  }
  public function Projections_Per_Film($inputs = [], $dpi = 150) {
    return $this->ccpcm->data->jsondb->get('planning','planning');
  }
  public function Accreditation($inputs = [], $dpi = 150) {
    $data = array();
    if (array_key_exists('ids', $inputs))
      $ids = $inputs['ids'];
    else {
      $ids = $this->ccpcm->data->jsondb->get_ids('accreditation');
    }
    foreach($ids as $id) {
      $data[] = $this->ccpcm->data->jsondb->get('accreditation',$id);
    }
    return $data;
  }
  public function Fiche_accredite($inputs = [], $dpi = 150) {
    $data = array();
    if (array_key_exists('ids', $inputs))
      $ids = $inputs['ids'];
    else {
      $ids = $this->ccpcm->data->jsondb->get_ids('accreditation');
    }
    foreach($ids as $id) {
      $data[] = $this->ccpcm->data->jsondb->get('accreditation',$id);
    }
    return $data;
  }
  public function Gravityform_winner($inputs = [], $dpi = 150) {
    if (array_key_exists('form_id' ,$inputs)) {
      return $this->ccpcm->data->jsondb->get('gravityform_winner', $inputs['form_id']);
    }
    return ['error' => 'pas de données'];
  }

  #RSFP
  public function Directory($inputs = [], $dpi = 150) {
    if (array_key_exists('form_id' ,$inputs)) {
      $d = $this->ccpcm->data->jsondb->get('directory', $inputs['form_id']);
      $terms = [
        'relationships_farm' => 'farm',
        'relationships_operation' => 'operation',
        'relationships_structure' => 'structure',
      ];
      foreach($terms as $fieldName => $termName) {
          if (array_key_exists($fieldName, $d)) {
              $termsData = [];
              if (is_string($d[$fieldName])) {
                  $d[$fieldName] = $this->ccpcm->data->jsondb->get($termName, $d[$fieldName]);
              } else {
                  $termIds = $d[$fieldName];
                  foreach($termIds as $termId)
                      $termsData[] = $this->ccpcm->data->jsondb->get($termName, $termId);
                  $d[$fieldName] = $termsData;
              }
          }
      }
      return $d;
    }
    return ['error' => 'pas de données'];
  }
}
