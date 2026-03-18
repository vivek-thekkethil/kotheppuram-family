<li class="mb-2">
    <div class="card card-bordered">
        <div class="card-innr py-2 px-3 d-flex align-items-center justify-content-between">
            <div>
                <strong>{{ $node['member']->name }}</strong>
                @if($node['member']->relationship_to_other && $node['member']->relatedMember)
                    <span class="text-light">({{ $node['member']->relationship_to_other }} of {{ $node['member']->relatedMember->name }})</span>
                @endif
            </div>
            <small class="text-light">{{ $node['member']->email ?: 'No email' }}</small>
        </div>
    </div>

    @if(!empty($node['children']))
        <ul class="list-unstyled pl-4 mt-2">
            @foreach($node['children'] as $child)
                @include('admin.partials.member-tree-node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
