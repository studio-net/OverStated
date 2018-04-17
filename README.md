A PHP Finite State Machine (With Laravel 5.4 integration)
==========================

Originaly based on https://github.com/daveawb/overstated

## Why use an FSM?
FSM's are a resource that allow developers tight control over resources within an application. There are many
articles detailing FSM's and what they are and what they're capable of so I won't go into much detail here.

## Requirements
- \>= PHP 7.0
- \>= Laravel 5.4

## Installation
### Composer
Add the following to your composer.json file

````json
{
    "require": {
        "studionet/overstated": "0.0.5"
    }
}
````

### Laravel Integration
Open `config/app.php` and register the required service provider.

```php
'providers' => [
    // ...
    OverStated\Providers\OverStatedServiceProvider::class,
]
```

## A Simple FSM

First you have to define states as classes, for example :

````php
use OverStated\States\State;

/**
 * Class Deleted
 */
class Deleted extends State {
	/** @var string $id */
	protected $id = 'deleted';
}
````

And transitions :

````php
use OverStated\Transitions\Transition;

/**
 * Class Delete
 */
class Delete extends Transition {
	// Transition slug
	public $slug = "delete";
	// Transition nice name
	public $name = "Delete";
	// Transition description
	public $description = "Delete a property";
	// Transition from which state(s) you can transit
	public $from = ["draft", "valid"];
	// Transition destination state
	public $to = "deleted";
}
````

The simple way to use it is in a Model, with the Stateful trait

````php
use OverStated\Support\Stateful;
use App\Fsm\Property\States;
use App\Fsm\Property\Transitions;

class Post extends Model {

	/** @type array $attributes default values */
	protected $attributes = [
		'status' => "draft",
	];

	// You have to define fields which will be controlled by FSMs
	// Here, it's the field 'status', which have states and a transition

	/** @var array $fsmDefinitions */
	protected $fsmDefinitions = [
		'status' => [
			'states' => [
				States\Draft::class,
				States\Valid::class,
				States\Deleted::class,
			],
			'transitions' => [
				Transitions\Delete::class,
			]
		]
	];
}
````

After that, you can update defined field like :
````php
// Will echo "draft"
echo $post->status;

// Change status
$post->fsmTransit("status", "delete");

// Will echo "deleted"
echo $post->status;

// Will throw an OverStated\Exceptions\TransitionException
$post->fsmTransit("status", "delete");
````


Or, you can manually create FSM

````php
use OverStated\Builders\GraphBuilder;
use App\Fsm\Property\States;
use App\Fsm\Property\Transitions;

$builder = new GraphBuilder();
$builder = $builder->create();

// Set states
$builder->state(States\Draft::class);
$builder->state(States\Valid::class);
$builder->state(States\Deleted::class);

// Set transitions
$builder->transition(Transitions\Delete::class);

// Get the machine from the builder
$fsm = $builder->getMachine();

// Initialise state
$fsm->initialise("draft");
````

You can now use it

````php
// Will output "draft"
$fsm->getState()->getId();

// Change state
$fsm->transition("delete");

// Will output "deleted"
$fsm->getState()->getId();

// Will throw an OverStated\Exceptions\TransitionException
$fsm->transition("delete");
````

## Hook
You can add a hook on a transition by overriding canTransit() fonction

````php
use OverStated\Transitions\Transition;

/**
 * Class Validate
 */
class Validate extends Transition {
	public $slug = "validate";
	public $name = "Validate";
	public $description = "Validate a post";
	public $from = ["draft"];
	public $to = "valid";

	/**
	 * @override
	 */
	public function canTransit() {
		// Model is available if you use Stateful trait on a Model
		$post = $this->machine->getModel();
		return empty($post->content);
	}

}
````