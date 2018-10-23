<?php
namespace OverStated\Support;

use OverStated\Builders\GraphBuilder;

/**
 * Stateful
 */
trait Stateful {

   /** @var Machine[] $fsms */
   protected $fsms = [];

   /**
    * Create FSMs to manage FSM-based-fields.
    */
   private function registerFsms() {

      if (empty($this->fsmDefinitions)) {
         throw new \Exception("FSM not defined.");
      }

      foreach ($this->fsmDefinitions as $field => $fsmInfos) {
         $builder = new GraphBuilder();
         $builder = $builder->create();

         foreach ($fsmInfos['states'] as $state) {
            $builder->state($state);
         }
         foreach ($fsmInfos['transitions'] as $transition) {
            $builder->transition($transition);
         }
         $builder->model($this);
         $this->fsms[$field] = $builder->getMachine();

         if (!empty($this->$field)) {
            $this->fsms[$field]->initialise($this->status);
         }
      }

   }

   /**
    * Get requested FSM.
    *
    * @param string $field
    * @return OverStated\Machine
    */
   public function getFsm($field) : \OverStated\Machine {

      if (empty($this->fsms)) {
         $this->registerFsms();
      }
      return $this->fsms[$field];
   }

   /**
    * Transit from the current state to another via a valid
    * transition.
    *
    * @throws \Exception if cant transit.
    * @param string $field
    * @param string $transition
    * @param array $arguments
    */
   public function fsmTransit($field, $transition, array $arguments = []) {
      $this->getFsm($field)->transition($transition, $arguments);
      $this->$field = $this->getFsm($field)->getState()->getId();
      $this->save();
   }

}
