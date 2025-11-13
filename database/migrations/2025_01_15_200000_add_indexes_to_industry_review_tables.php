<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();

        // Add indexes for yachts table
        $yachtIndexes = [
            'yachts_name_index' => 'name',
            'yachts_home_port_index' => 'home_port',
            'yachts_builder_index' => 'builder',
            'yachts_rating_avg_index' => 'rating_avg',
            'yachts_reviews_count_index' => 'reviews_count',
        ];

        foreach ($yachtIndexes as $indexName => $column) {
            $exists = $connection->select(
                "SELECT COUNT(*) as count FROM information_schema.statistics 
                 WHERE table_schema = ? AND table_name = 'yachts' AND index_name = ?",
                [$databaseName, $indexName]
            );
            
            if ($exists[0]->count == 0) {
                $connection->statement("CREATE INDEX {$indexName} ON yachts ({$column})");
            }
        }

        // Add indexes for marinas table
        $marinaIndexes = [
            'marinas_name_index' => 'name',
            'marinas_city_index' => 'city',
            'marinas_country_index' => 'country',
            'marinas_reviews_count_index' => 'reviews_count',
        ];

        foreach ($marinaIndexes as $indexName => $column) {
            $exists = $connection->select(
                "SELECT COUNT(*) as count FROM information_schema.statistics 
                 WHERE table_schema = ? AND table_name = 'marinas' AND index_name = ?",
                [$databaseName, $indexName]
            );
            
            if ($exists[0]->count == 0) {
                $connection->statement("CREATE INDEX {$indexName} ON marinas ({$column})");
            }
        }
    }

    public function down(): void
    {
        Schema::table('yachts', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['home_port']);
            $table->dropIndex(['builder']);
            $table->dropIndex(['rating_avg']);
            $table->dropIndex(['reviews_count']);
        });

        Schema::table('marinas', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['city']);
            $table->dropIndex(['country']);
            $table->dropIndex(['rating_avg']);
            $table->dropIndex(['reviews_count']);
        });
    }
};

