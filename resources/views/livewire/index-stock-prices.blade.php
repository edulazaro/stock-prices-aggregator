<?php

use Livewire\Volt\Component;
use Illuminate\Database\Eloquent\Collection;

use App\Contracts\StockPriceRepositoryInterface;

new class extends Component
{
    /** @var StockPriceRepositoryInterface The repository for managing stock prices. */
    protected StockPriceRepositoryInterface $stockPriceRepository;

    /** @var Collection Collection of stock prices. */
    public Collection $stockPrices;

    /**
     * Fetches and sets all stock prices from the repository.
     */
    public function getStockPrices(): void
    {
        $this->stockPrices = $this->stockPriceRepository->all();
    }

    /**
     * Initializes the component with the StockPriceRepository.
     *
     * @param StockPriceRepositoryInterface $stockPriceRepository The repository for stock prices.
     */
    public function boot(StockPriceRepositoryInterface $stockPriceRepository): void
    {
        $this->stockPriceRepository = $stockPriceRepository;
    }

    /**
     * Perform initial setup when the component is mounted.
     *
     * Retrieves applications from the database and initializes the component's state.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->getStockPrices();
    }
}; ?>

<div class="x-applications-index-applications block items-center justify-between pb-4">
    <div class="overflow-visible">
        <div class="relative sm:rounded-lg overflow-x-auto">
            <table class="w-full text-sm text-gray-500">
                <thead class="text-gray-700 uppercase bg-gray-50 text-left">
                    <tr>
                        <th scope="col" class="px-6 py-4">
                            Symbol
                        </th>
                        <th scope="col" class="px-6 py-4">
                            Price
                        </th>
                        <th scope="col" class="px-6 py-4">
                            Previous Price
                        </th>
                        <th scope="col" class="px-6 py-4">
                            Change
                        </th>
                        <th scope="col" class="px-6 py-4">
                            Volume
                        </th>
                        <th scope="col" class="px-6 py-4">
                            Open
                        </th>
                        <th scope="col" class="px-6 py-4">
                            High
                        </th>
                        <th scope="col" class="px-6 py-4">
                            Low
                        </th>
                    </tr>
                </thead>
                <tbody wire:poll.30s="getStockPrices">
                    @foreach ($stockPrices as $key => $stockPrice)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">
                            {{ $stockPrice->symbol }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $stockPrice->price }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $stockPrice->previous_close }}
                        </td>
                        <td class="px-6 py-4 group flex">
                            @if ($stockPrice->percentageChange > 0)
                            <svg class="w-6 h-6 text-green-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v13m0-13 4 4m-4-4-4 4" />
                            </svg>
                            @elseif ($stockPrice->percentageChange < 0) <svg class="w-6 h-6 text-red-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m0 14-4-4m4 4 4-4" />
                                </svg>
                                @else
                                <span class="text-2xl text-blue-500 mr-2 ml-1 -mt-1">=</span>
                                @endif
                                <span class="mt-1">{{ $stockPrice->percentageChange }}%</span>
                        </td>
                        <td class="px-6 py-4">
                            {{ $stockPrice->volume }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $stockPrice->open }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $stockPrice->high }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $stockPrice->low }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>