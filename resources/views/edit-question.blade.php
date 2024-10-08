<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tag Create</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<div class="container text-center">
    <h1 class="display-1 mt-5">Edit</h1>
    <div class="my-5">
    </div>
    <div class="input-group justify-content-center">
        <form action="{{ route('update', ['question' => $question]) }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="input-group-text mb-3">
                <div class="mt-5 mx-auto">
                    <label class="form-label" for="text">Message:</label>
                    <textarea class="form-control" name="text" id="text" type="text-area">{{ $question->body }}</textarea>
                </div>
            </div>
            <div class="input-group">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox" name="correctAnswerIds[]" value="0" aria-label="Checkbox for following text input" @checked(in_array('0', $correctAnswers))>
                </div>
                <input id="answer_1" type="text" class="form-control" name="answers[0]" aria-label="Text input with checkbox" value="{{ $answers[0] ?? null}}">

                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox" name="correctAnswerIds[]" value='1' aria-label="Checkbox for following text input" @checked(in_array('1', $correctAnswers))>
                </div>
                <input id="answer_2" type="text" class="form-control" name="answers[1]" aria-label="Text input with checkbox" value="{{ $answers[1] ?? null}}">

                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox" name="correctAnswerIds[]" value='2' aria-label="Checkbox for following text input" @checked(in_array('2', $correctAnswers))>
                </div>
                <input id="answer_3" type="text" class="form-control" name="answers[2]" aria-label="Text input with checkbox" value="{{ $answers[2] ?? null}}">

                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox" name="correctAnswerIds[]" value='3' aria-label="Checkbox for following text input" @checked(in_array('3', $correctAnswers))>
                </div>
                <input id="answer_4" type="text" class="form-control" name="answers[3]" aria-label="Text input with checkbox" value="{{ $answers[3] ?? null}}">

                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox" name="correctAnswerIds[]" value='4' aria-label="Checkbox for following text input" @checked(in_array('4', $correctAnswers))>
                </div>
                <input id="answer_4" type="text" class="form-control" name="answers[4]" aria-label="Text input with checkbox" value="{{ $answers[4] ?? null}}">

                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox" name="correctAnswerIds[]" value='5' aria-label="Checkbox for following text input" @checked(in_array('5', $correctAnswers))>
                </div>
                <input id="answer_4" type="text" class="form-control" name="answers[5]" aria-label="Text input with checkbox" value="{{ $answers[5] ?? null}}">

                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox" name="correctAnswerIds[]" value='6' aria-label="Checkbox for following text input" @checked(in_array('6', $correctAnswers))>
                </div>
                <input id="answer_4" type="text" class="form-control" name="answers[6]" aria-label="Text input with checkbox" value="{{ $answers[6] ?? null}}">

                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox" name="correctAnswerIds[]" value='7' aria-label="Checkbox for following text input" @checked(in_array('7', $correctAnswers))>
                </div>
                <input id="answer_4" type="text" class="form-control" name="answers[7]" aria-label="Text input with checkbox" value="{{ $answers[7] ?? null}}">

            </div>
                <div class="col mb-5 mx-auto">
                <label for="image" class="form-label"></label>
                <input class="form-control" name="image" type="file" id="image"/>
            </div>
            <div>
                <button type="submit" class="btn btn-primary mb-3">Confirm</button>
            </div>
        </form>
    </div>
</div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
