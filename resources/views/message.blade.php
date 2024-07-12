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
    <h1 class="display-1 mt-5">Message</h1>
    <div class="my-5">
    </div>
    <div class="input-group justify-content-center">
    <form action="{{ route('store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="input-group-text mb-3">
            <div class="mt-5 mx-auto">
                <label class="form-label" for="text">Message:</label>
                <textarea class="form-control" name="text" id="text" type="text"></textarea>
            </div>
        </div>
        <div class="input-group">
        <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" name="answers[1][true]" value="{{true}}" aria-label="Checkbox for following text input">
        </div>
        <input id="answer_1" type="text" class="form-control" name="answers[1][text]" aria-label="Text input with checkbox">

        <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" name="answers[2][true]" value='{{true}}' aria-label="Checkbox for following text input">
        </div>
        <input id="answer_2" type="text" class="form-control" name="answers[2][text]" aria-label="Text input with checkbox">

        <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" name="answers[3][true]" value='{{true}}' aria-label="Checkbox for following text input">
        </div>
        <input id="answer_3" type="text" class="form-control" name="answers[3][text]" aria-label="Text input with checkbox">

        <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" name="answers[4][true]" value='{{true}}' aria-label="Checkbox for following text input">
        </div>
        <input id="answer_4" type="text" class="form-control" name="answers[4][text]" aria-label="Text input with checkbox">
        </div>
        <div class="col mb-5 mx-auto">
            <label for="image" class="form-label"></label>
            <input class="form-control" name="image" type="file" id="image"/>
        </div>
        <div>
            <button type="submit" class="btn btn-primary mb-3">Confirm</button>
        </div>
    </form>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Images</th>
                <th scope="col">Text</th>
                <th scope="col">Answers</th>
                <th scope="col">Correct</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($questions as $question)
                <tr>
                    <th scope="row" class="col-2">
                        <div id="{{ $question->id }}" class="carousel carousel-dark slide">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="{{ url('storage/images/'.$question->images()->first()?->name) }}" alt="Image" class="img-fluid">
                                </div>
                                @foreach($question->images as $image)
                                    <div class="carousel-item">
                                        <img src="{{ url('storage/images/'.$image?->name)}}" class="img-thumbnail" width="300" height="250" alt="image">
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#{{ $question->id }}" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#{{ $question->id }}" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </th>
                    <th scope="row">{{$question->body}}</th>
                    <th scope="row">{{$question->answers}}</th>
                    <th scope="row">{{$question->correct_answer}}</th>
                    <th scope="row">
                        <form id="question-edit" method="get" action="{{ route('update', ['question' => $question]) }}">
                            @method('GET')
                            @csrf
                            <button class="btn btn-primary">Edit</button>
                        </form>
                        <form id="question-delete" method="post" action="{{ route('delete', ['question' => $question]) }}">
                            @method('DELETE')
                            @csrf
                            <button class="btn btn-danger">Delete</button>
                        </form>
                    </th>
                </tr>
            @endforeach
            </tbody>
        </table>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
