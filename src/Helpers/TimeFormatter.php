<?php
class TimeFormatter
{
    /**
     * Format minutes into a human readable string
     */
    public static function formatEstimatedTime($minutes)
    {
        if ($minutes < 60) {
            return "{$minutes}m";
        }
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours}h";
    }

    /**
     * Format seconds into a human readable string
     */
    public static function formatDuration($seconds)
    {
        $seconds = abs($seconds);

        if ($seconds < 60) {
            return $seconds . "s";
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $secs = $seconds % 60;
            return "{$minutes}m {$secs}s";
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $secs = $seconds % 60;
            return "{$hours}h {$minutes}m {$secs}s";
        }
    }
}
