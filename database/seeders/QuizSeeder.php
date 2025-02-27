<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run()
    {
        // Create a quiz
        $quiz = Quiz::create([
            'title' => 'Assessment Quiz',
            'description' => 'This quiz will test your knowledge and skills. You must pass to proceed to the next stage.',
            'time_limit' => 30, // 30 minutes
            'passing_score' => 7, // Pass with 7 points
        ]);
        
        // Question 1
        $question1 = Question::create([
            'quiz_id' => $quiz->id,
            'content' => 'What is the main purpose of Laravel\'s Eloquent ORM?',
            'points' => 1,
            'type' => 'single',
        ]);
        
        Answer::create([
            'question_id' => $question1->id,
            'content' => 'To handle HTTP requests',
            'is_correct' => false,
        ]);
        
        Answer::create([
            'question_id' => $question1->id,
            'content' => 'To interact with the database using object-oriented syntax',
            'is_correct' => true,
        ]);
        
        Answer::create([
            'question_id' => $question1->id,
            'content' => 'To render frontend views',
            'is_correct' => false,
        ]);
        
        Answer::create([
            'question_id' => $question1->id,
            'content' => 'To handle authentication',
            'is_correct' => false,
        ]);
        
        // Question 2
        $question2 = Question::create([
            'quiz_id' => $quiz->id,
            'content' => 'Which of the following are valid Laravel artisan commands?',
            'points' => 2,
            'type' => 'multiple',
        ]);
        
        Answer::create([
            'question_id' => $question2->id,
            'content' => 'php artisan make:model',
            'is_correct' => true,
        ]);
        
        Answer::create([
            'question_id' => $question2->id,
            'content' => 'php artisan create:view',
            'is_correct' => false,
        ]);
        
        Answer::create([
            'question_id' => $question2->id,
            'content' => 'php artisan migrate',
            'is_correct' => true,
        ]);
        
        Answer::create([
            'question_id' => $question2->id,
            'content' => 'php artisan generate:route',
            'is_correct' => false,
        ]);
        
        // Add more questions as needed
        // Question 3
        $question3 = Question::create([
            'quiz_id' => $quiz->id,
            'content' => 'What does MVC stand for?',
            'points' => 1,
            'type' => 'single',
        ]);
        
        Answer::create([
            'question_id' => $question3->id,
            'content' => 'Model View Controller',
            'is_correct' => true,
        ]);
        
        Answer::create([
            'question_id' => $question3->id,
            'content' => 'Most Valuable Code',
            'is_correct' => false,
        ]);
        
        Answer::create([
            'question_id' => $question3->id,
            'content' => 'Multiple Visual Components',
            'is_correct' => false,
        ]);
        
        Answer::create([
            'question_id' => $question3->id,
            'content' => 'Main Virtual Container',
            'is_correct' => false,
        ]);
        
        // Question 4
        $question4 = Question::create([
            'quiz_id' => $quiz->id,
            'content' => 'Which of the following are valid ways to define routes in Laravel?',
            'points' => 2,
            'type' => 'multiple',
        ]);
        
        Answer::create([
            'question_id' => $question4->id,
            'content' => 'Using Route::get() method',
            'is_correct' => true,
        ]);
        
        Answer::create([
            'question_id' => $question4->id,
            'content' => 'Using Route::resource() method',
            'is_correct' => true,
        ]);
        
        Answer::create([
            'question_id' => $question4->id,
            'content' => 'Using View::create() method',
            'is_correct' => false,
        ]);
        
        Answer::create([
            'question_id' => $question4->id,
            'content' => 'Using Route::controller() method',
            'is_correct' => false,
        ]);
    }
}