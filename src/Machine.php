<?php namespace OverStated;

use OverStated\Contracts\EventInterface;
use OverStated\Contracts\StructureInterface;
use OverStated\Exceptions\UninitialisedException;
use OverStated\States\State;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Machine
* @package FSM
*/
class Machine
{
   /**
    * The name of the state machine
    *
    * @var string
    */
   protected $id;

   /**
    * @var EventInterface
    */
   protected $events;

   /**
    * The state structure
    *
    * @var StructureInterface
    */
   private $structure;

   /**
    * List of historical transitions
    *
    * @var array
    */
   private $history = [];

   /**
    * The active state
    *
    * @var State
    */
   private $state;

   /**
    * Construct the machine
    *
    * @param EventInterface $events
    */
   public function __construct(EventInterface $events)
   {
      $this->events = $events;
      $this->listen('initialise', [$this, 'initialise']);
   }

   /**
    * Set the initial state manually
    *
    * @param null $state
    */
   public function initialise($state = null)
   {
      $this->state = $this->structure->getInitialState($state);

      $this->forget('initialise');

   }

   /**
    * Transition from the current state to another via a valid
    * transition.
    *
    * @param string $transition
    * @param array $arguments
    * @return bool
    * @throws UninitialisedException
    */
   public function transition($transition, array $arguments = [])
   {
      $transition = $this->structure->getTransition($transition);
		$transition->setArguments($arguments);
      $destState = $transition->getTo();

      if (! $this->state) {
            throw new UninitialisedException('FSM is not initialised.');
      }

      $currentState = $this->getState()->getId();

      if (!$this->structure->canTransitionFrom($currentState, $destState)) {
            throw new Exceptions\TransitionException(
               sprintf(
                  "Transition '%s' to status '%s' is impossible from status '%s'",
                  $transition->getSlug(),
                  $destState,
                  $this->getState()->getId()
               )
            );
      }

      $transition->onTransit();

      array_push($this->history, $currentState);
      $this->setState($this->structure->getState($destState));

      // Emit a transition event
      $this->fire("transition", [
         "from"       => $this->structure->getState($currentState),
         "transition" => $transition
      ]);

   }

   /**
    * Handle a method on the current state.
    *
    * @param string $handle
    * @param array $args
    * @return mixed
    *
    * @throws UninitialisedException
    */
   public function handle($handle, $args = [])
   {
      if (! $this->state) {
            throw new UninitialisedException('FSM is not initialised.');
      }

      array_unshift($args, $this->state);

      $data = $this->execHandle($handle, $args);

      $this->fire("handled.{$handle}", [$this->state, $data]);

      return $data;
   }

   /**
    * Execute the handler on the state
    *
    * @param $handle
    * @param array $args
    *
    * @return mixed
    */
   protected function execHandle($handle, $args = [])
   {
      $result = call_user_func_array([$this->state, $handle], $args);

      return is_null($result) ? : $result;
   }

   /**
    * Get the history array
    *
    * @return array
    */
   public function getHistory()
   {
      return $this->history;
   }

   /**
    * Get a list of all possible state transitions.
    *
    * @param $options array
    * @return array
    */
   public function getTransitions($options = [])
   {
      $options = $options + [
            "onlyValid"   => false,
      ];

      $transitions = $this->structure->getTransitionsFrom(
            $this->getState()->getId());

      $validTransitions = [];
      foreach ($transitions as $transition) {
            if ($transition->checkCanTransit()) {
               $validTransitions[] = $transition;
            }
      }

      return $options["onlyValid"] ? $validTransitions : $transitions;
   }

   /**
    * Get the current state instance
    *
    * @return State
    */
   public function getState()
   {
      return $this->state;
   }

   /**
    * Set the current state
    *
    * @param State $state
    */
   public function setState(State $state)
   {
      $this->state = $state;
   }

   /**
    * Set associated model.
    * @param Model $model
    */
   public function setModel(Model $model)
   {
      $this->model = $model;
   }

   /**
    * Get associated model.
    *
    * @return Model
    */
   public function getModel() : Model
   {
      return $this->model;
   }

   /**
    * Get the machines structure instance.
    *
    * @return mixed
    */
   public function getStructure()
   {
      return $this->structure;
   }

   /**
    * @param StructureInterface $structure
    */
   public function setStructure(StructureInterface $structure)
   {
      $this->structure = $structure;
   }

   /**
    * Get the machines id
    *
    * @return string
    */
   public function getId()
   {
      if (isset($this->id)) {
            return $this->id;
      }

      return $this->id = str_replace('\\', '', strtolower(class_basename($this)));
   }

   /**
    * Set the machines ID
    *
    * @param $id
    */
   public function setId($id)
   {
      $this->id = $id;
   }


   /**
    * Fire an event bound to this machines ID.
    *
    * @param $type
    * @param array $args
    */
   public function fire($type, $args = [])
   {
      array_unshift($args, $this);

      $this->events->fire($this->appendId($type), $args);
   }

   /**
    * Listen for events of a type bound to this machines ID.
    *
    * @param $type
    * @param callable $closure
    */
   public function listen($type, callable $closure)
   {
      $this->events->listen($this->appendId($type), $closure);
   }

   /**
    * Forget an event or events that were previously
    * registered.
    *
    * @param $events
    */
   public function forget($events)
   {
      if (is_array($events)) {
            foreach ($events as $event) {
               $events = $this->appendId($event);
            }
      } else {
            $events = $this->appendId($events);
      }

      $this->events->forget($events);
   }

   /**
    * Append the machines ID to a string using
    * dot notation.
    *
    * @param $string
    * @return string
    */
   protected function appendId($string)
   {
      return "{$this->getId()}.{$string}";
   }

   /**
    * Validates model against state validation rules
    *
    * @return bool
    * @throws \Illuminate\Validation\ValidationException
    */
   public function validates($stateKey = null)
   {

      if ($stateKey === null) {
         $state = $this->getState();
      } else {
         $state = $this->structure->getInitialState($stateKey);
      }
      $validator = \Illuminate\Support\Facades\Validator::make(
         $this->getModel()->toArray(),
         $state->getValidationRules(),
         $state->getValidationMessages()
      );

      if ($validator->fails()) {
         throw new \Illuminate\Validation\ValidationException($validator);
      }
   }

   /**
    * Check if the model is valid (easy way to check if the state's constraints
    * are valids)
    *
    * @return bool
    */
   public function isModelValid($stateKey = null)
   {
      try {
         $this->validates($stateKey);
      } catch (\Illuminate\Validation\ValidationException $exception) {
         return false;
      }

      return true;
   }
}
