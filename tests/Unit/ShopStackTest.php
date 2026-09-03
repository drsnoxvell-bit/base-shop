<?php

namespace Tests\Unit;

use App\Support\ShopStack;
use Tests\TestCase;

class ShopStackTest extends TestCase
{
    public function test_install_choices_are_numbered_from_one_to_five(): void
    {
        $choices = ShopStack::installChoices();

        $this->assertSame([1, 2, 3, 4, 5], array_keys($choices));
        $this->assertStringStartsWith('1. ', $choices[1]);
        $this->assertStringStartsWith('5. ', $choices[5]);
    }

    public function test_resolve_accepts_number_or_stack_name(): void
    {
        $this->assertSame('blade', ShopStack::resolve(1));
        $this->assertSame('blade', ShopStack::resolve('blade'));
        $this->assertSame('inertia-react', ShopStack::resolve(3));
        $this->assertSame('spa-react', ShopStack::resolve('5'));
        $this->assertNull(ShopStack::resolve('0'));
        $this->assertNull(ShopStack::resolve('vue'));
    }
}
