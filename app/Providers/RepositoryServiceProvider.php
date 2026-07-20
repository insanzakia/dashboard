<?php

namespace App\Providers;

use App\Repositories\Contracts\AlatRepositoryInterface;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\DataPemeriksaanRepositoryInterface;
use App\Repositories\Contracts\InventarisAlatRepositoryInterface;
use App\Repositories\Contracts\LabkesmasRepositoryInterface;
use App\Repositories\Contracts\PemenuhanAlatRepositoryInterface;
use App\Repositories\Contracts\WilayahRepositoryInterface;
use App\Repositories\Eloquent\AlatRepository;
use App\Repositories\Eloquent\DashboardRepository;
use App\Repositories\Eloquent\DataPemeriksaanRepository;
use App\Repositories\Eloquent\InventarisAlatRepository;
use App\Repositories\Eloquent\LabkesmasRepository;
use App\Repositories\Eloquent\PemenuhanAlatRepository;
use App\Repositories\Eloquent\WilayahRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Binding kontrak repository → implementasi Eloquent.
 * Mengganti implementasi (mis. untuk testing/mock) cukup di satu tempat ini.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $bindings = [
        WilayahRepositoryInterface::class => WilayahRepository::class,
        DashboardRepositoryInterface::class => DashboardRepository::class,
        LabkesmasRepositoryInterface::class => LabkesmasRepository::class,
        DataPemeriksaanRepositoryInterface::class => DataPemeriksaanRepository::class,
        AlatRepositoryInterface::class => AlatRepository::class,
        InventarisAlatRepositoryInterface::class => InventarisAlatRepository::class,
        PemenuhanAlatRepositoryInterface::class => PemenuhanAlatRepository::class,
    ];
}
