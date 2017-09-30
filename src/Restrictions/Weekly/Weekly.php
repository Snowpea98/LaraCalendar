<?php


namespace Uruloke\LaraCalendar\Restrictions\Weekly;



use Illuminate\Contracts\Support\Arrayable;
use Uruloke\LaraCalendar\Carbon;
use Uruloke\LaraCalendar\Contracts\Days\Day;
use Uruloke\LaraCalendar\Contracts\Restrictions\Parseable;
use Uruloke\LaraCalendar\Contracts\Restrictions\Recurrence\Recurrencable;
use Uruloke\LaraCalendar\Contracts\Restrictions\Restrictionable;
use Uruloke\LaraCalendar\EventCollection;
use Uruloke\LaraCalendar\Models\Event;

class Weekly implements Recurrencable, Parseable
{
	/** @var Day */
	public $day;
	/**
	 * @var int
	 */
	private $everyNWeek;

	public function __construct ($day, int $everyNWeek = null)
	{
		$this->day = $day;
		$this->everyNWeek = $everyNWeek;
	}

	public function passes(Carbon $currentDay, EventCollection $events) : bool {
		if(is_null($this->everyNWeek)) {
			return $this->isSameDay($currentDay);
		}

		if($events->isEmpty()) {
			return $this->isSameDay($currentDay);
		}
		/** @var Event $last */
		$last = $events->last();
		if($last->startsAt()->weekOfYear+$this->everyNWeek == $currentDay->weekOfYear) {
			return $this->isSameDay($currentDay);
		}
		return false;
	}

	/**
	 * @param Carbon $currentDay
	 * @return bool
	 */
	private function isSameDay (Carbon $currentDay): bool
	{
		return $currentDay->dayOfWeek == $this->day::dayAsNumber();
	}

	public function __toString (): string
	{
		if(is_null($this->everyNWeek)) {
			return "w{{$this->day::dayAsNumber()}}";
		}
		return "w{{$this->day::dayAsNumber()},{$this->everyNWeek}}";
	}

	/**
	 * @return Restrictionable
	 *
	 * Parse in the parameters for converting from string to the
	 * class.
	 */
	public static function parse (): Restrictionable
	{
		$args = func_get_args();

		return new Weekly(int_to_day($args[0]), $args[1] ?? null);
	}
}