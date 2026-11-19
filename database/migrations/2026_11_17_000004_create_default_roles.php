<?php

use App\Support\Access;
use Illuminate\Database\Migrations\Migration;

/** The built-in roles (Super Admin, Customer, Manager, Booking Staff, Content Editor) and their permissions. */
return new class extends Migration
{
    public function up(): void
    {
        Access::sync();
    }

    public function down(): void
    {
        // Roles are removed with the permission tables.
    }
};
