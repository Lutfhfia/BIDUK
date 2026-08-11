<?php

namespace Tests\Feature;

use App\Models\StudentPreviousEducation;
use Tests\TestCase;

class StudentPreviousEducationModelTest extends TestCase
{
    public function test_previous_education_model_uses_expected_table_name(): void
    {
        $model = new StudentPreviousEducation();

        $this->assertSame('student_previous_educations', $model->getTable());
    }
}
