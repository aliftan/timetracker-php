<?php
class Validator
{
    private $errors = [];
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Require a field to be present and not empty
     */
    public function required($field, $message = null)
    {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = $message ?? ucfirst($field) . ' is required';
        }
        return $this;
    }

    /**
     * Validate email format
     */
    public function email($field, $message = null)
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? 'Please enter a valid email address';
        }
        return $this;
    }

    /**
     * Check minimum length
     */
    public function min($field, $length, $message = null)
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must be at least $length characters";
        }
        return $this;
    }

    /**
     * Check if a field value is unique in a database table
     */
    public function unique($field, $table, $message = null)
    {
        if (isset($this->data[$field])) {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM $table WHERE $field = :value");
            $stmt->bindValue(':value', $this->data[$field], SQLITE3_TEXT);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_ASSOC);

            if ($row['count'] > 0) {
                $this->errors[$field] = $message ?? ucfirst($field) . ' is already taken';
            }
        }
        return $this;
    }

    /**
     * Check if password confirmation matches
     */
    public function match($field, $matchField, $message = null)
    {
        if (isset($this->data[$field]) && isset($this->data[$matchField])) {
            if ($this->data[$field] !== $this->data[$matchField]) {
                $this->errors[$field] = $message ?? 'Passwords do not match';
            }
        }
        return $this;
    }

    /**
     * Check if there are any validation errors
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Get all validation errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get first error message
     */
    public function getFirstError()
    {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
}
