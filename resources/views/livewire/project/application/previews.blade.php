<div>
    <livewire:project.application.preview.form :application="$application" />
    <div>
        <div class="flex items-center gap-2">
            <h3>Pull Requests on Git</h3>
            <x-forms.button wire:click="load_prs">Load Pull Requests (open)
            </x-forms.button>
        </div>
        @isset($rate_limit_remaining)
            <div class="pt-1 text-sm">Requests remaning till rate limited by Git: {{ $rate_limit_remaining }}</div>
        @endisset
        @if (count($pull_requests) > 0)
            <div wire:loading.remove wire:target='load_prs' class="flex gap-4 py-4">
                <div class="overflow-x-auto table-md">
                    <table class="table">
                        <thead>
                            <tr class="text-warning border-coolgray-200">
                                <th>PR Number</th>
                                <th>PR Title</th>
                                <th>Git</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pull_requests as $pull_request)
                                <tr class="border-coolgray-200">
                                    <th>{{ data_get($pull_request, 'number') }}</th>
                                    <td>{{ data_get($pull_request, 'title') }}</td>
                                    <td>
                                        <a target="_blank" class="text-xs"
                                            href="{{ data_get($pull_request, 'html_url') }}">Open PR on
                                            Git
                                            <x-external-link />
                                        </a>
                                    </td>
                                    <td class="flex items-center justify-center gap-2">
                                        <x-forms.button
                                            wire:click="deploy('{{ data_get($pull_request, 'number') }}', '{{ data_get($pull_request, 'html_url') }}')">
                                            Deploy
                                        </x-forms.button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
    @if ($application->previews->count() > 0)
        <h4 class="pt-4">Preview Deployments</h4>
        <div class="flex gap-6 text-sm">
            @foreach ($application->previews as $preview)
                <div class="flex flex-col p-4 bg-coolgray-200 " x-init="$wire.loadStatus('{{ data_get($preview, 'pull_request_id') }}')">
                    <div class="flex gap-2">PR #{{ data_get($preview, 'pull_request_id') }} |
                        @if (data_get($preview, 'status') === 'running')
                            <div class="flex items-center gap-2">
                                <div class="badge badge-success badge-xs"></div>
                                <div class="text-xs font-medium tracking-wide">Running</div>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <div class="badge badge-error badge-xs"></div>
                                <div class="text-xs font-medium tracking-wide">Stopped</div>
                            </div>
                        @endif
                        @if (data_get($preview, 'status') !== 'exited')
                            | <a target="_blank" href="{{ data_get($preview, 'fqdn') }}">Open Preview
                                <x-external-link />
                            </a>
                        @endif
                        |
                        <a target="_blank" href="{{ data_get($preview, 'pull_request_html_url') }}">Open PR on Git
                            <x-external-link />
                        </a>
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <x-forms.button wire:click="deploy({{ data_get($preview, 'pull_request_id') }})">
                            @if (data_get($preview, 'status') === 'exited')
                                Deploy
                            @else
                                Redeploy
                            @endif
                        </x-forms.button>
                        @if (data_get($preview, 'status') !== 'exited')
                            <x-forms.button wire:click="stop({{ data_get($preview, 'pull_request_id') }})">Remove
                                Preview
                            </x-forms.button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
