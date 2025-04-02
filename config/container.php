<?php

use FrameJam\Core\Queue\Queue;

return [
    // ... existing code ...
    
    'queue' => function ($container) {
        return new Queue();
    },
    
    // ... existing code ...
]; 