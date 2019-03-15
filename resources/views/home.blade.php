@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <td colspan="7">League Table</td>
                    </tr>
                    <tr>
                        <td>Teams</td>
                        <td>PTS</td>
                        <td>P</td>
                        <td>W</td>
                        <td>D</td>
                        <td>L</td>
                        <td>GD</td>
                    </tr>
                    </thead>
                    <tbody id="league-table-body">
                    @if (!empty($league))
                        @foreach ($league as $lg)
                            <tr>
                                <td>{{$lg->name}}</td>
                                <td>{{isset($lg->points)?$lg->points:'0'}}</td>
                                <td>{{isset($lg->played)?$lg->played:'0'}}</td>
                                <td>{{isset($lg->won)?$lg->won:'0'}}</td>
                                <td>{{isset($lg->drawn)?$lg->drawn:'0'}}</td>
                                <td>{{isset($lg->lost)?$lg->lost:'0'}}</td>
                                <td>{{isset($lg->goal_difference)?$lg->goal_difference:'0'}}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table table-hover" id="weekly" data-week-id="2" real=1>
                    <thead>
                    <tr>
                        <td colspan="4">Match Results</td>
                    </tr>
                    </thead>
                    <tbody id="weekly-matches">
                    <tr>
                        <td colspan="3">1st Week Match Result</td>
                    </tr>

                    @if (!empty($matches))
                        @foreach ($matches[1] as $results)
                            <tr>
                                <td>{{$results['home_team']}}</td>
                                <td>
                                    <div style="float:left" id="home-goal" data-match-id="{{$results['id']}}">
                                        {{$results['home_goal']}}
                                    </div>
                                    <div style="float:left" id="t">-</div>
                                    <div style="float:left" id="away-goal" data-match-id="{{$results['id']}}">
                                        {{$results['away_goal']}}
                                    </div>
                                </td>
                                <td>{{$results['away_team']}}</td>
                            </tr>

                        @endforeach
                    @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <button class="btn btn-success pull-left" id="play-weekly"
                                        @if($results['played'] == 1) style="display:none" @endif>Play Weekly
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-2">
                <table class="table table-hover">
                    <tbody id="predictions">
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <table class="table">
                    <tr>
                        <td>
                            <button class="btn btn-success pull-left" id="play-all">Play all</button>
                            <button class="btn btn-primary pull-right" id="see-next-week">Next Week</button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <td colspan="2">Fixture</td>
                        <td>
                            <button class="btn btn-danger" id="reset">Reset Fixture</button>
                        </td>
                    </tr>
                    </thead>
                    <tbody id="table-body">
                    @if (!empty($weeks))
                        @foreach($weeks as $week)
                            <tr>
                                <td colspan="3">{{$week->name}} Matches</td>
                            </tr>
                            @if (!empty($fixture))
                                @foreach ($fixture[$week->id] as $results)

                                    <tr>
                                        <td> {{$results['home_team']}}</td>
                                        <td>{{$results['home_goal']}} - {{$results['away_goal']}}</td>
                                        <td> {{$results['away_team']}}</td>
                                    </tr>

                                @endforeach
                            @endif
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {

            $(document).on("dblclick", 'div#home-goal,div#away-goal', function (event) {
                $(this).css('border', '1px solid black');
                $(this).css('width', '30px');
                $(this).attr('contentEditable', true);
            });

            $(document).on("blur", 'div#home-goal,div#away-goal', function (event) {
                $(this).attr('contentEditable', true);
                $.post("/api/matches/" + $(this).data('match-id') + "/" + $(this).attr('id').replace('-', '_') + "/" + $(this)[0].innerHTML, {_method: 'patch'}, function () {
                    refreshFixture();
                    refreshLeague();
                    $('#weekly').data('week-id', 1);
                    $('#weekly').attr('real', 1);
                    getNextMatches();
                    predictions();
                });

                //update matches
            });

            $("span").keypress(function (e) {
                if ((event.which < 48 || event.which > 57))
                    e.preventDefault();
            });

            predictions();

            $("#play-all").click(function () {
                $.post("api/play", function () {
                    refreshFixture();
                    refreshLeague();
                    $('#weekly').data('week-id', 1);
                    $('#weekly').attr('real', 1);
                    getNextMatches();
                    predictions();
                });
            });
            $("#reset").click(function () {
                $.post("api/reset", function () {
                    refreshFixture();
                    refreshLeague();
                    $('#weekly').data('week-id', 1);
                    getNextMatches();
                    $('#weekly').attr('real', 1);
                    predictions();
                });
            });

            $("#see-next-week").click(function () {
                getNextMatches();
            });

            $("#play-weekly").click(function () {
                playWeekly();
                refreshFixture();
                refreshLeague();
                predictions();
            });

            function refreshFixture() {
                $.getJSON("/api/fixture", function (data) {
                    var showData = $('#table-body');

                    showData.empty();
                    showData.hide();
                    $.each(data.weeks, function (i, week) {
                        var html = "";
                        html += "<tr><td colspan='3'>" + week.name + " Matches</td></tr>";
                        $.each(data.items[week.id], function (i, item) {
                            html += "<tr>";
                            html += "<td>" + item.home_team + "</td>";
                            html += "<td>" + item.home_goal + " - ";
                            html += item.away_goal + "</td>";
                            html += "<td>" + item.away_team + "</td>";
                            html += "</tr>";

                        });
                        showData.append(html);
                    });

                    showData.show('slow');
                });
            }

            function refreshLeague() {
                $.getJSON("/api/league", function (data) {
                    var showData = $('#league-table-body');
                    showData.empty();
                    showData.hide();
                    $.each(data, function (i, item) {
                        var html = "";
                        html += "<tr>";
                        html += "<td>" + item.name + "</td>";
                        html += "<td>" + item.points + "</td>";
                        html += "<td>" + item.played + "</td>";
                        html += "<td>" + item.won + "</td>";
                        html += "<td>" + item.drawn + "</td>";
                        html += "<td>" + item.lost + "</td>";
                        html += "<td>" + item.goal_difference + "</td>";
                        html += "</tr>";
                        showData.append(html);
                    });
                    showData.show('slow');

                });
            }

            function getNextMatches() {
                var weekID = $('table#weekly').data('week-id');
                $.getJSON("/api/matches/" + weekID, function (data) {
                    var showData = $('#weekly-matches');
                    showData.empty();
                    showData.hide();
                    $.each(data.matches, function (i, item) {
                        var html = "";
                        if (i === 0) {
                            html += "<tr>"
                                + '<td colspan="3">' + item.name + ' Match Result</td>'
                                + '</tr>';
                        }
                        html += '<tr>'
                            + '<td>' + item.home_team + '</td>'
                            + '<td><div style="float:left" id="home-goal" data-match-id="' + item.id + '">' + item.home_goal + '</div><div style="float:left" id="t">-</div>  <div style="float:left" id="away-goal"  data-match-id="' + item.id + '">' + item.away_goal + '</div></td>'
                            + '<td>' + item.away_team + '</td>'
                            + '</tr>'
                        showData.append(html);
                        if (item.played === 1)
                            $('#play-weekly').hide();
                        else
                            $('#play-weekly').show();

                        predictions(item);
                    });
                    showData.show('slow');
                    if (weekID + 1 === 7) {
                        $('#see-next-week').hide();
                    }
                });
                $('#weekly').data('week-id', (weekID + 1));
                $('#weekly').attr('real', (parseInt($('#weekly').attr('real')) + 1))
            }

            function playWeekly() {
                var weekId = $('#weekly').attr('real');

                $.post("/api/play/" + weekId, function (data) {
                    var showData = $('#weekly-matches');
                    showData.empty();
                    showData.hide();

                    $.each(data.matches, function (i, item) {
                        var html = "";
                        if (i === 0) {
                            html += "<tr>"
                                + '<td colspan="3">' + item.name + ' Matches</td>'
                                + '</tr>';
                        }
                        html += '<tr>'
                            + '<td>' + item.home_team + '</td>'
                            + '<td><span id="home-goal" data-match-id="' + item.id + '">' + item.home_goal + '</span> - <span id="away-goal"  data-match-id="' + item.id + '">' + item.away_goal + '</span></td>'
                            + '<td>' + item.away_team + '</td>'
                            + '</tr>'
                        showData.append(html);

                        if (item.played === 1)
                            $('#play-weekly').hide();
                    });
                    showData.show('slow');

                });
            }

            function predictions(weekData) {
                $.get("/api/predictions", function (data) {
                    var showData = $('#predictions');
                    showData.empty();
                    showData.hide();

                    var week = $('#weekly').data('week-id');
                    if (weekData) {
                        week = weekData.name;
                    }
                    $.each(data.items, function (i, item) {
                        var html = "";
                        if (i === 0) {
                            html += '<tr><td colspan="2">' + week + ' Predictions of Championship</td></tr>';
                        }
                        html += '<tr>'
                            + '<td>' + item[0] + '</td>'
                            + '<td>' + item[1] + '</td>'
                            + '</tr>'
                        showData.append(html);
                    });
                    showData.show('slow');
                });
            }
        });
    </script>

@endsection
