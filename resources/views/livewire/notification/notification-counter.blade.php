<div wire:poll.30s>
    @if($unreadCount > 0)
    <span class="absolute -top-1 sm:-top-2 -right-1 sm:-right-2 bg-red-500 text-white rounded-full w-3 h-3 xs:w-4 xs:h-4 sm:w-5 sm:h-5 flex items-center justify-center text-[8px] xs:text-[10px] sm:text-xs">
        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
    </span>
    @endif
</div>
