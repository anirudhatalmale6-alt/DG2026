<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cims_tyredash_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('full_size', 50)->comment('e.g. 205/65R16');
            $table->integer('width')->comment('Section width in mm, e.g. 205');
            $table->integer('profile')->comment('Aspect ratio, e.g. 65');
            $table->string('construction', 5)->default('R')->comment('R=Radial, D=Diagonal');
            $table->integer('rim_diameter')->comment('Rim diameter in inches, e.g. 16');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('full_size');
            $table->index(['width', 'profile', 'rim_diameter']);
        });

        // Seed common South African tyre sizes
        $now = now();
        $sizes = [
            // Passenger popular sizes
            ['155/65R14', 155, 65, 'R', 14],
            ['165/65R14', 165, 65, 'R', 14],
            ['175/65R14', 175, 65, 'R', 14],
            ['185/65R14', 185, 65, 'R', 14],
            ['175/65R15', 175, 65, 'R', 15],
            ['185/65R15', 185, 65, 'R', 15],
            ['195/65R15', 195, 65, 'R', 15],
            ['205/65R15', 205, 65, 'R', 15],
            ['195/55R15', 195, 55, 'R', 15],
            ['205/55R16', 205, 55, 'R', 16],
            ['205/65R16', 205, 65, 'R', 16],
            ['215/55R16', 215, 55, 'R', 16],
            ['215/60R16', 215, 60, 'R', 16],
            ['225/55R16', 225, 55, 'R', 16],
            ['205/50R17', 205, 50, 'R', 17],
            ['215/55R17', 215, 55, 'R', 17],
            ['225/45R17', 225, 45, 'R', 17],
            ['225/50R17', 225, 50, 'R', 17],
            ['225/55R17', 225, 55, 'R', 17],
            ['235/45R17', 235, 45, 'R', 17],
            ['225/40R18', 225, 40, 'R', 18],
            ['225/45R18', 225, 45, 'R', 18],
            ['235/40R18', 235, 40, 'R', 18],
            ['245/45R18', 245, 45, 'R', 18],
            ['255/35R18', 255, 35, 'R', 18],
            ['225/35R19', 225, 35, 'R', 19],
            ['235/35R19', 235, 35, 'R', 19],
            ['245/40R19', 245, 40, 'R', 19],
            ['255/35R19', 255, 35, 'R', 19],
            ['245/35R20', 245, 35, 'R', 20],
            ['275/40R20', 275, 40, 'R', 20],
            // SUV / 4x4 popular sizes
            ['215/65R16', 215, 65, 'R', 16],
            ['235/65R17', 235, 65, 'R', 17],
            ['235/60R18', 235, 60, 'R', 18],
            ['245/65R17', 245, 65, 'R', 17],
            ['255/60R18', 255, 60, 'R', 18],
            ['255/65R17', 255, 65, 'R', 17],
            ['265/65R17', 265, 65, 'R', 17],
            ['265/60R18', 265, 60, 'R', 18],
            ['275/65R17', 275, 65, 'R', 17],
            ['275/70R16', 275, 70, 'R', 16],
            ['285/65R17', 285, 65, 'R', 17],
            ['285/60R18', 285, 60, 'R', 18],
            // Light Truck / Van
            ['195R14C', 195, 0, 'R', 14],
            ['205/65R16C', 205, 65, 'R', 16],
            ['215/65R16C', 215, 65, 'R', 16],
            ['225/65R16C', 225, 65, 'R', 16],
            ['235/65R16C', 235, 65, 'R', 16],
        ];

        foreach ($sizes as $s) {
            DB::table('cims_tyredash_sizes')->insert([
                'full_size' => $s[0],
                'width' => $s[1],
                'profile' => $s[2],
                'construction' => $s[3],
                'rim_diameter' => $s[4],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cims_tyredash_sizes');
    }
};
