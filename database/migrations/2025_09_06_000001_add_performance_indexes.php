<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Performance optimization: Add missing indexes for commonly queried columns
     */
    public function up(): void
    {
        // Critical: Polymorphic relationship index for mt_status table
        if (Schema::hasTable('mt_status')) {
            Schema::table('mt_status', function (Blueprint $table) {
                $table->index(['layanan_type', 'layanan_id'], 'mt_status_polymorphic_index');
                $table->index('created_at', 'mt_status_created_at_index');
            });
        }

        // permohonan_informasi_publik - UUID foreign key indexes
        if (Schema::hasTable('permohonan_informasi_publik')) {
            Schema::table('permohonan_informasi_publik', function (Blueprint $table) {
                if (!$this->hasIndex('permohonan_informasi_publik', 'permohonan_informasi_publik_user_id_index')) {
                    $table->index('user_id', 'permohonan_informasi_publik_user_id_index');
                }
                $table->index(['user_id', 'created_at'], 'permohonan_user_created_index');
            });
        }

        // keberatan_informasi - Foreign key indexes
        if (Schema::hasTable('keberatan_informasi')) {
            Schema::table('keberatan_informasi', function (Blueprint $table) {
                if (!$this->hasIndex('keberatan_informasi', 'keberatan_informasi_user_id_index')) {
                    $table->index('user_id', 'keberatan_informasi_user_id_index');
                }
                if (!$this->hasIndex('keberatan_informasi', 'keberatan_informasi_permohonan_id_index')) {
                    $table->index('permohonan_id', 'keberatan_informasi_permohonan_id_index');
                }
            });
        }

        // publications - Category and status indexes
        if (Schema::hasTable('publications')) {
            Schema::table('publications', function (Blueprint $table) {
                if (!$this->hasIndex('publications', 'publications_category_id_index')) {
                    $table->index('category_id', 'publications_category_id_index');
                }
                if (!$this->hasIndex('publications', 'publications_subcategory_id_index')) {
                    $table->index('subcategory_id', 'publications_subcategory_id_index');
                }
                $table->index('status', 'publications_status_index');
                $table->index(['category_id', 'status'], 'publications_category_status_index');
            });
        }

        // publication_subcategories - Foreign key index
        if (Schema::hasTable('publication_subcategories')) {
            Schema::table('publication_subcategories', function (Blueprint $table) {
                if (!$this->hasIndex('publication_subcategories', 'publication_subcategories_category_id_index')) {
                    $table->index('category_id', 'publication_subcategories_category_id_index');
                }
            });
        }

        // aduans - Status and category indexes
        if (Schema::hasTable('aduans')) {
            Schema::table('aduans', function (Blueprint $table) {
                $table->index('status', 'aduans_status_index');
                $table->index('kategori', 'aduans_kategori_index');
                $table->index(['status', 'created_at'], 'aduans_status_created_index');
            });
        }

        // skts - Common query indexes
        if (Schema::hasTable('skts')) {
            Schema::table('skts', function (Blueprint $table) {
                if (!$this->hasIndex('skts', 'skts_id_pemohon_index')) {
                    $table->index('id_pemohon', 'skts_id_pemohon_index');
                }
                $table->index('status', 'skts_status_index');
                $table->index(['id_pemohon', 'created_at'], 'skts_pemohon_created_index');
            });
        }

        // skls - Common query indexes
        if (Schema::hasTable('skls')) {
            Schema::table('skls', function (Blueprint $table) {
                if (!$this->hasIndex('skls', 'skls_id_pemohon_index')) {
                    $table->index('id_pemohon', 'skls_id_pemohon_index');
                }
                $table->index('status', 'skls_status_index');
                $table->index(['id_pemohon', 'created_at'], 'skls_pemohon_created_index');
            });
        }

        // spts - Foreign key and status indexes
        if (Schema::hasTable('spts')) {
            Schema::table('spts', function (Blueprint $table) {
                $table->index('status', 'spts_status_index');
                $table->index('created_at', 'spts_created_at_index');
            });
        }

        // lapor_athg - Additional indexes for filtering
        if (Schema::hasTable('lapor_athg')) {
            Schema::table('lapor_athg', function (Blueprint $table) {
                $table->index('bidang', 'lapor_athg_bidang_index');
                $table->index('jenis_athg', 'lapor_athg_jenis_index');
                $table->index('tingkat_urgensi', 'lapor_athg_urgensi_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mt_status')) {
            Schema::table('mt_status', function (Blueprint $table) {
                $table->dropIndex('mt_status_polymorphic_index');
                $table->dropIndex('mt_status_created_at_index');
            });
        }

        if (Schema::hasTable('permohonan_informasi_publik')) {
            Schema::table('permohonan_informasi_publik', function (Blueprint $table) {
                $table->dropIndex('permohonan_informasi_publik_user_id_index');
                $table->dropIndex('permohonan_user_created_index');
            });
        }

        if (Schema::hasTable('keberatan_informasi')) {
            Schema::table('keberatan_informasi', function (Blueprint $table) {
                $table->dropIndex('keberatan_informasi_user_id_index');
                $table->dropIndex('keberatan_informasi_permohonan_id_index');
            });
        }

        if (Schema::hasTable('publications')) {
            Schema::table('publications', function (Blueprint $table) {
                $table->dropIndex('publications_category_id_index');
                $table->dropIndex('publications_subcategory_id_index');
                $table->dropIndex('publications_status_index');
                $table->dropIndex('publications_category_status_index');
            });
        }

        if (Schema::hasTable('publication_subcategories')) {
            Schema::table('publication_subcategories', function (Blueprint $table) {
                $table->dropIndex('publication_subcategories_category_id_index');
            });
        }

        if (Schema::hasTable('aduans')) {
            Schema::table('aduans', function (Blueprint $table) {
                $table->dropIndex('aduans_status_index');
                $table->dropIndex('aduans_kategori_index');
                $table->dropIndex('aduans_status_created_index');
            });
        }

        if (Schema::hasTable('skts')) {
            Schema::table('skts', function (Blueprint $table) {
                $table->dropIndex('skts_id_pemohon_index');
                $table->dropIndex('skts_status_index');
                $table->dropIndex('skts_pemohon_created_index');
            });
        }

        if (Schema::hasTable('skls')) {
            Schema::table('skls', function (Blueprint $table) {
                $table->dropIndex('skls_id_pemohon_index');
                $table->dropIndex('skls_status_index');
                $table->dropIndex('skls_pemohon_created_index');
            });
        }

        if (Schema::hasTable('spts')) {
            Schema::table('spts', function (Blueprint $table) {
                $table->dropIndex('spts_status_index');
                $table->dropIndex('spts_created_at_index');
            });
        }

        if (Schema::hasTable('lapor_athg')) {
            Schema::table('lapor_athg', function (Blueprint $table) {
                $table->dropIndex('lapor_athg_bidang_index');
                $table->dropIndex('lapor_athg_jenis_index');
                $table->dropIndex('lapor_athg_urgensi_index');
            });
        }
    }

    /**
     * Check if an index exists on a table
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $index) {
            if ($index['name'] === $indexName) {
                return true;
            }
        }
        return false;
    }
};
