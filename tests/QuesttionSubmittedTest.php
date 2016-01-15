<?php namespace MXTranslator\Tests;
use \MXTranslator\Events\QuestionSubmitted as Event;

class QuestionSubmittedTest extends AttemptStartedTest {
    protected static $recipe_name = 'attempt_question_completed';

    /**
     * Sets up the tests.
     * @override TestCase
     */
    public function setup() {
        $this->event = new Event($this->repo);
    }

    protected function constructInput() {
        $input = array_merge(parent::constructInput(), [
            'questions' => $this->constructQuestions()
        ]);

        $input['attempt']->questions = $this->constructQuestionAttempts();

        return $input;
    }

    private function constructQuestionAttempts() {
        return [
            $this->constructQuestionAttempt(1),
            $this->constructQuestionAttempt(2),
            $this->constructQuestionAttempt(3)
        ];
    }

    private function constructQuestionAttempt($index) {
        return (object) [
            'id' => 1,
            'questionid' => 1,
            'maxmark' => '5.0000000',
            'steps' => [
                (object)[
                    'sequencenumber' => 1,
                    'state' => 'todo',
                    'timecreated' => '1433946000',
                    'fraction' => null
                ],
                (object)[
                    'sequencenumber' => 2,
                    'state' => 'gradedright',
                    'timecreated' => '1433946701',
                    'fraction' => '1.0000000'
                ],
            ],
            'responsesummary' => 'test answer',
            'rightanswer' => 'test answer'
        ];
    }

    private function constructQuestions() {
        return [
            $this->constructQuestion(1),
            $this->constructQuestion(2),
            $this->constructQuestion(3)
        ];
    }

    private function constructQuestion($index) {
        return (object) [
            'id' => 1,
            'name' => 'test question {$index}',
            'questiontext' => 'test questiontext',
            'answers' => [
                '1'=> (object)[
                    'id' => '1',
                    'answer' => 'test answer'
                ],
                '2'=> (object)[
                    'id' => '2',
                    'answer' => 'wrong test answer'
                ]
            ]
        ];
    }

    protected function assertOutput($input, $output) {
        parent::assertOutput($input, $output);
        $this->assertAttempt($input['attempt'], $output);
    }

    protected function assertAttempt($input, $output) {
        parent::assertAttempt($input, $output);
        $this->assertEquals((float) $input->sumgrades, $output['attempt_score_raw']);
        $this->assertEquals($input->state === 'finished', $output['attempt_completed']);
        $this->assertQuestionAttempt($input->questions, $output);
    }

    protected function assertQuestionAttempt($input, $output) {
        $this->assertEquals((float) $input->maxmark, $output['attempt_score_max']);
        $this->assertEquals((float) $input->steps[1]->fraction, $output['attempt_score_scaled']);
        $this->assertEquals((float) $input->maxmark, $output['attempt_score_max']);
    }

    protected function assertQuestion($input, $output) {
        
    }
}
