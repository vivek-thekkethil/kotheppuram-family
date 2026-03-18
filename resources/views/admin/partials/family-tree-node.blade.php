<li>
    <div class="family-couple">
        <div class="family-person">
            <div class="d-flex align-items-center mb-2">
                @if($node['member']->photo_path)
                    <img class="photo" src="{{ asset($node['member']->photo_path) }}" alt="{{ $node['member']->name }}">
                @else
                    <span class="photo-placeholder"><em class="ti ti-user"></em></span>
                @endif
                <div>
                    <div class="name">{{ $node['member']->name }}</div>
                        <div class="meta text-light">{{ $node['member']->date_of_birth?->format('Y-m-d') ?: 'DOB: -' }}</div>
                </div>
            </div>
            <div class="meta text-light">{{ $node['member']->email ?: 'No email' }}</div>
            <div class="meta text-light">{{ $node['member']->phone ?: 'No phone' }}</div>
            @if($node['member']->relationship_to_other && $node['member']->relatedMember)
                <div class="meta text-light relation-hover">Relation: {{ $node['member']->relationship_to_other }} of {{ $node['member']->relatedMember->name }}</div>
            @endif
        </div>

        @if(!empty($node['spouse']))
            <span class="family-couple-link">
                <span class="family-love-icon"><em class="ti ti-heart"></em></span>
            </span>
            <div class="family-person">
                <div class="d-flex align-items-center mb-2">
                    @if($node['spouse']->photo_path)
                        <img class="photo" src="{{ asset($node['spouse']->photo_path) }}" alt="{{ $node['spouse']->name }}">
                    @else
                        <span class="photo-placeholder"><em class="ti ti-user"></em></span>
                    @endif
                    <div>
                        <div class="name">{{ $node['spouse']->name }}</div>
                        <div class="meta text-light">{{ $node['spouse']->date_of_birth?->format('Y-m-d') ?: 'DOB: -' }}</div>
                    </div>
                </div>
                <div class="meta text-light">{{ $node['spouse']->email ?: 'No email' }}</div>
                <div class="meta text-light">{{ $node['spouse']->phone ?: 'No phone' }}</div>
                <div class="meta text-light">Partner</div>
                @if($node['spouse']->relationship_to_other && $node['spouse']->relatedMember)
                    <div class="meta text-light relation-hover">Relation: {{ $node['spouse']->relationship_to_other }} of {{ $node['spouse']->relatedMember->name }}</div>
                @endif
            </div>
        @endif
    </div>

    @if(!empty($node['children']))
        <ul class="family-descendants" style="--branch-origin: {{ !empty($node['spouse']) ? '116px' : '50%' }};">
            @foreach($node['children'] as $child)
                @include('admin.partials.family-tree-node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
